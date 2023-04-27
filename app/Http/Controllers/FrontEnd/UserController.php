<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\MiscellaneousController;
use App\Http\Helpers\BasicMailer;
use App\Http\Helpers\UploadFile;
use App\Http\Requests\UserProfileRequest;
use App\Models\BasicSettings\Basic;
use App\Models\BasicSettings\MailTemplate;
use App\Models\Instrument\EquipmentBooking;
use App\Models\Shop\Product;
use App\Models\Shop\ProductOrder;
use App\Models\User;
use App\Rules\MatchEmailRule;
use App\Rules\MatchOldPasswordRule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class UserController extends Controller
{
  public function __construct()
  {
    $bs = DB::table('basic_settings')
      ->select('facebook_app_id', 'facebook_app_secret', 'google_client_id', 'google_client_secret')
      ->first();

    Config::set('services.facebook.client_id', $bs->facebook_app_id);
    Config::set('services.facebook.client_secret', $bs->facebook_app_secret);
    Config::set('services.facebook.redirect', url('user/login/facebook/callback'));

    Config::set('services.google.client_id', $bs->google_client_id);
    Config::set('services.google.client_secret', $bs->google_client_secret);
    Config::set('services.google.redirect', url('user/login/google/callback'));
  }

  public function login(Request $request)
  {
    $misc = new MiscellaneousController();

    $language = $misc->getLanguage();

    $queryResult['seoInfo'] = $language->seoInfo()->select('meta_keyword_login', 'meta_description_login')->first();

    $queryResult['pageHeading'] = $misc->getPageHeading($language);

    $queryResult['bgImg'] = $misc->getBreadcrumb();

    // get the status of digital product (exist or not in the cart)
    if (!empty($request->input('digital_item'))) {
      $queryResult['digitalProductStatus'] = $request->input('digital_item');
    }

    $queryResult['bs'] = Basic::query()->select('google_recaptcha_status', 'facebook_login_status', 'google_login_status')->first();

    return view('frontend.login', $queryResult);
  }

  public function redirectToFacebook()
  {
    return Socialite::driver('facebook')->redirect();
  }

  public function handleFacebookCallback()
  {
    return $this->authenticationViaProvider('facebook');
  }

  public function redirectToGoogle()
  {
    return Socialite::driver('google')->redirect();
  }

  public function handleGoogleCallback()
  {
    return $this->authenticationViaProvider('google');
  }

  public function authenticationViaProvider($driver)
  {
    // get the url from session which will be redirect after login
    if (Session::has('redirectTo')) {
      $redirectURL = Session::get('redirectTo');
    } else {
      $redirectURL = route('user.dashboard');
    }

    $responseData = Socialite::driver($driver)->user();
    $userInfo = $responseData->user;

    $isUser = User::query()->where('email', '=', $userInfo['email'])->first();

    if (!empty($isUser)) {
      // log in
      if ($isUser->status == 1) {
        Auth::login($isUser);

        return redirect($redirectURL);
      } else {
        Session::flash('error', 'Sorry, your account has been deactivated.');

        return redirect()->route('user.login');
      }
    } else {
      // get user avatar and save it
      $avatar = $responseData->getAvatar();
      $fileContents = file_get_contents($avatar);

      $avatarName = $responseData->getId() . '.jpg';
      $path = public_path('assets/img/users/');

      file_put_contents($path . $avatarName, $fileContents);

      // sign up
      $user = new User();

      if ($driver == 'facebook') {
        $user->first_name = $userInfo['name'];
      } else {
        $user->first_name = $userInfo['given_name'];
        $user->last_name = $userInfo['family_name'];
      }

      $user->image = $avatarName;
      $user->username = $userInfo['id'];
      $user->email = $userInfo['email'];
      $user->email_verified_at = date('Y-m-d H:i:s');
      $user->status = 1;
      $user->provider = ($driver == 'facebook') ? 'facebook' : 'google';
      $user->provider_id = $userInfo['id'];
      $user->save();

      Auth::login($user);

      return redirect($redirectURL);
    }
  }

  public function loginSubmit(Request $request)
  {
    // get the url from session which will be redirect after login
    if ($request->session()->has('redirectTo')) {
      $redirectURL = $request->session()->get('redirectTo');
    } else {
      $redirectURL = null;
    }

    $info = Basic::select('google_recaptcha_status')->first();

    $rules = [
      'email' => 'required|email:rfc,dns',
      'password' => 'required'
    ];

    if ($info->google_recaptcha_status == 1) {
      $rules['g-recaptcha-response'] = 'required|captcha';
    }

    $messages = [];

    if ($info->google_recaptcha_status == 1) {
      $messages['g-recaptcha-response.required'] = 'Please verify that you are not a robot.';
      $messages['g-recaptcha-response.captcha'] = 'Captcha error! try again later or contact site admin.';
    }

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return redirect()->route('user.login')->withErrors($validator->errors())->withInput();
    }

    // get the email and password which has provided by the user
    $credentials = $request->only('email', 'password');

    // login attempt
    if (Auth::guard('web')->attempt($credentials)) {
      $authUser = Auth::guard('web')->user();

      // first, check whether the user's email address verified or not
      if (is_null($authUser->email_verified_at)) {
        Session::flash('error', 'Please, verify your email address.');

        // logout auth user as condition not satisfied
        Auth::guard('web')->logout();

        return redirect()->back();
      }

      // second, check whether the user's account is active or not
      if ($authUser->status == 0) {
        Session::flash('error', 'Sorry, your account has been deactivated.');

        // logout auth user as condition not satisfied
        Auth::guard('web')->logout();

        return redirect()->back();
      }

      // otherwise, redirect auth user to next url
      if (is_null($redirectURL)) {
        return redirect()->route('user.dashboard');
      } else {
        // before, redirect to next url forget the session value
        $request->session()->forget('redirectTo');

        return redirect($redirectURL);
      }
    } else {
      Session::flash('error', 'Incorrect email address or password!');

      return redirect()->back();
    }
  }

  public function forgetPassword()
  {
    $misc = new MiscellaneousController();

    $language = $misc->getLanguage();

    $queryResult['seoInfo'] = $language->seoInfo()->select('meta_keyword_forget_password', 'meta_description_forget_password')->first();

    $queryResult['pageHeading'] = $misc->getPageHeading($language);

    $queryResult['bgImg'] = $misc->getBreadcrumb();

    return view('frontend.forget-password', $queryResult);
  }

  public function forgetPasswordMail(Request $request)
  {
    $rules = [
      'email' => [
        'required',
        'email:rfc,dns',
        new MatchEmailRule('user')
      ]
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors())->withInput();
    }

    $user = User::query()->where('email', '=', $request->email)->first();

    // store user email in session to use it later
    $request->session()->put('userEmail', $user->email);

    // get the mail template information from db
    $mailTemplate = MailTemplate::query()->where('mail_type', '=', 'reset_password')->first();
    $mailData['subject'] = $mailTemplate->mail_subject;
    $mailBody = $mailTemplate->mail_body;

    // get the website title info from db
    $info = Basic::select('website_title')->first();

    $name = $user->first_name . ' ' . $user->last_name;

    $link = '<a href=' . url("user/reset-password") . '>Click Here</a>';

    $mailBody = str_replace('{customer_name}', $name, $mailBody);
    $mailBody = str_replace('{password_reset_link}', $link, $mailBody);
    $mailBody = str_replace('{website_title}', $info->website_title, $mailBody);

    $mailData['body'] = $mailBody;

    $mailData['recipient'] = $user->email;

    $mailData['sessionMessage'] = 'A mail has been sent to your email address.';
  

    BasicMailer::sendMail($mailData);

    return redirect()->back();
  }

  public function resetPassword()
  {
    $misc = new MiscellaneousController();

    $bgImg = $misc->getBreadcrumb();

    return view('frontend.reset-password', compact('bgImg'));
  }

  public function resetPasswordSubmit(Request $request)
  {
    if ($request->session()->has('userEmail')) {
      // get the user email from session
      $emailAddress = $request->session()->get('userEmail');

      $rules = [
        'new_password' => 'required|confirmed',
        'new_password_confirmation' => 'required'
      ];

      $messages = [
        'new_password.confirmed' => 'Password confirmation failed.',
        'new_password_confirmation.required' => 'The confirm new password field is required.'
      ];

      $validator = Validator::make($request->all(), $rules, $messages);

      if ($validator->fails()) {
        return redirect()->back()->withErrors($validator->errors());
      }

      $user = User::query()->where('email', '=', $emailAddress)->first();

      $user->update([
        'password' => Hash::make($request->new_password)
      ]);

      Session::flash('success', 'Password updated successfully.');
    } else {
      Session::flash('error', 'Something went wrong!');
    }

    return redirect()->route('user.login');
  }

  public function signup()
  {
    $misc = new MiscellaneousController();

    $language = $misc->getLanguage();

    $queryResult['seoInfo'] = $language->seoInfo()->select('meta_keyword_signup', 'meta_description_signup')->first();

    $queryResult['pageHeading'] = $misc->getPageHeading($language);

    $queryResult['bgImg'] = $misc->getBreadcrumb();

    $queryResult['recaptchaInfo'] = Basic::select('google_recaptcha_status')->first();

    return view('frontend.signup', $queryResult);
  }

  public function signupSubmit(Request $request)
  {
    $info = Basic::select('google_recaptcha_status', 'website_title')->first();

    // validation start
    $rules = [
      'username' => 'required|unique:users|max:255',
      'email' => 'required|email:rfc,dns|unique:users|max:255',
      'password' => 'required|confirmed',
      'password_confirmation' => 'required'
    ];

    if ($info->google_recaptcha_status == 1) {
      $rules['g-recaptcha-response'] = 'required|captcha';
    }

    $messages = [
      'password_confirmation.required' => 'The confirm password field is required.'
    ];

    if ($info->google_recaptcha_status == 1) {
      $messages['g-recaptcha-response.required'] = 'Please verify that you are not a robot.';
      $messages['g-recaptcha-response.captcha'] = 'Captcha error! try again later or contact site admin.';
    }

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors())->withInput();
    }
    // validation end

    $user = new User();
    $user->username = $request->username;
    $user->email = $request->email;
    $user->password = Hash::make($request->password);

    // first, generate a random string
    $randStr = Str::random(20);

    // second, generate a token
    $token = md5($randStr . $request->username . $request->email);

    $user->verification_token = $token;
    $user->save();

    /**
     * prepare a verification mail and, send it to user to verify his/her email address,
     * get the mail template information from db
     */
    $mailTemplate = MailTemplate::query()->where('mail_type', '=', 'verify_email')->first();
    $mailData['subject'] = $mailTemplate->mail_subject;
    $mailBody = $mailTemplate->mail_body;

    $link = '<a href=' . url("user/signup-verify/" . $token) . '>Click Here</a>';

    $mailBody = str_replace('{username}', $request->username, $mailBody);
    $mailBody = str_replace('{verification_link}', $link, $mailBody);
    $mailBody = str_replace('{website_title}', $info->website_title, $mailBody);
    
    // $mailData['body'] = $mailBody;

    // $mailData['recipient'] = $request->email;

    // $mailData['sessionMessage'] = 'A verification link has been sent to your email address.';

    // BasicMailer::sendMail($mailData);

    return redirect()->back();
  }

  public function signupVerify(Request $request, $token)
  {
    try {
      $user = User::where('verification_token', $token)->firstOrFail();

      // after verify user email, put "null" in the "verification token"
      $user->update([
        'email_verified_at' => date('Y-m-d H:i:s'),
        'status' => 1,
        'verification_token' => null
      ]);

      Session::flash('success', 'Your email has been verified.');

      // after email verification, authenticate this user
      Auth::guard('web')->login($user);

      return redirect()->route('user.dashboard');
    } catch (ModelNotFoundException $e) {
      Session::flash('error', 'Could not verify your email address!');

      return redirect()->route('user.signup');
    }
  }

  public function redirectToDashboard()
  {
    $misc = new MiscellaneousController();

    $queryResult['bgImg'] = $misc->getBreadcrumb();

    $user = Auth::guard('web')->user();

    $queryResult['authUser'] = $user;

    $queryResult['numOfOrders'] = $user->productOrder()->count();

    $queryResult['numOfBookings'] = $user->equipmentBooking()->count();

    return view('frontend.user.dashboard', $queryResult);
  }

  public function editProfile()
  {
    $misc = new MiscellaneousController();

    $queryResult['bgImg'] = $misc->getBreadcrumb();

    $queryResult['authUser'] = Auth::guard('web')->user();

    return view('frontend.user.edit-profile', $queryResult);
  }

  public function updateProfile(UserProfileRequest $request)
  {
    $authUser = Auth::guard('web')->user();

    if ($request->hasFile('image')) {
      $newImg = $request->file('image');
      $oldImg = $authUser->image;
      $imageName = UploadFile::update(public_path('assets/img/users/'), $newImg, $oldImg);
    }

    $authUser->update($request->except('image') + [
      'image' => $request->hasFile('image') ? $imageName : $authUser->image
    ]);

    Session::flash('success', 'Your profile has been updated successfully.');

    return redirect()->back();
  }

  public function changePassword()
  {
    $misc = new MiscellaneousController();

    $bgImg = $misc->getBreadcrumb();

    return view('frontend.user.change-password', compact('bgImg'));
  }

  public function updatePassword(Request $request)
  {
    $rules = [
      'current_password' => [
        'required',
        new MatchOldPasswordRule('user')
      ],
      'new_password' => 'required|confirmed',
      'new_password_confirmation' => 'required'
    ];

    $messages = [
      'new_password.confirmed' => 'Password confirmation failed.',
      'new_password_confirmation.required' => 'The confirm new password field is required.'
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator->errors());
    }

    $user = Auth::guard('web')->user();

    $user->update([
      'password' => Hash::make($request->new_password)
    ]);

    Session::flash('success', 'Password updated successfully.');

    return redirect()->back();
  }

  public function bookings()
  {
    $misc = new MiscellaneousController();

    $queryResult['bgImg'] = $misc->getBreadcrumb();

    $authUser = Auth::guard('web')->user();

    $bookings = $authUser->equipmentBooking()->orderByDesc('id')->get();

    $language = $misc->getLanguage();

    $bookings->map(function ($booking) use ($language) {
      $equipment = $booking->equipmentInfo()->first();
      $booking['equipmentInfo'] = $equipment->content()->where('language_id', $language->id)
        ->select('title', 'slug')
        ->first();
    });

    $queryResult['bookings'] = $bookings;

    return view('frontend.user.equipment-bookings', $queryResult);
  }

  public function bookingDetails($id)
  {
    $misc = new MiscellaneousController();

    $queryResult['bgImg'] = $misc->getBreadcrumb();

    $details = EquipmentBooking::query()->find($id);
    if ($details) {
      $queryResult['details'] = $details;

      if ($details->user_id != Auth::guard('web')->user()->id) {
        return redirect()->route('user.dashboard');
      }

      $queryResult['language'] = $misc->getLanguage();

      $queryResult['tax'] = Basic::select('equipment_tax_amount')->first();

      return view('frontend.user.booking-details', $queryResult);
    } else {
      return view('errors.404');
    }
  }

  public function orders()
  {
    $misc = new MiscellaneousController();

    $queryResult['bgImg'] = $misc->getBreadcrumb();

    $authUser = Auth::guard('web')->user();

    $queryResult['orders'] = $authUser->productOrder()->orderByDesc('id')->get();

    return view('frontend.user.product-orders', $queryResult);
  }

  public function orderDetails($id)
  {
    $misc = new MiscellaneousController();

    $queryResult['bgImg'] = $misc->getBreadcrumb();

    $language = $misc->getLanguage();

    $order = ProductOrder::query()->find($id);
    if ($order) {
      if ($order->user_id != Auth::guard('web')->user()->id) {
        return redirect()->route('user.dashboard');
      }

      $queryResult['details'] = $order;

      $queryResult['tax'] = Basic::select('product_tax_amount')->first();

      $items = $order->item()->get();

      $items->map(function ($item) use ($language) {
        $product = $item->productInfo()->first();
        $item['price'] = $product->current_price;
        $item['productType'] = $product->product_type;
        $item['inputType'] = $product->input_type;
        $item['link'] = $product->link;
        $content = $product->content()->where('language_id', $language->id)->first();
        $item['productTitle'] = $content->title;
        $item['slug'] = $content->slug;
      });

      $queryResult['items'] = $items;

      return view('frontend.user.order-details', $queryResult);
    } else {
      return view('errors.404');
    }
  }

  public function downloadProduct($id, Request $request)
  {
    $misc = new MiscellaneousController();

    $language = $misc->getLanguage();

    $product = Product::find($id);

    $slug = $product->content()->where('language_id', $language->id)->pluck('slug')->first();

    $pathToFile = public_path('assets/file/products/') . $product->file;

    try {
      return response()->download($pathToFile, $slug . '.zip');
    } catch (FileNotFoundException $e) {
      Session::flash('error', 'Sorry, this file does not exist anymore!');

      return redirect()->back();
    }
  }

  public function logoutSubmit(Request $request)
  {
    Auth::guard('web')->logout();

    if ($request->session()->has('redirectTo')) {
      $request->session()->forget('redirectTo');
    }

    return redirect()->route('user.login');
  }
}
