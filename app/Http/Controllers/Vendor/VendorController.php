<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\MiscellaneousController;
use App\Models\Admin;
use App\Models\BasicSettings\Basic;
use App\Models\BasicSettings\MailTemplate;
use App\Models\Instrument\Equipment;
use App\Models\Instrument\EquipmentBooking;
use App\Models\Language;
use App\Models\Transcation;
use App\Models\Vendor;
use App\Models\VendorInfo;
use App\Rules\MatchEmailRule;
use App\Rules\MatchOldPasswordRule;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PHPMailer\PHPMailer\PHPMailer;

class VendorController extends Controller
{
    //signup
    public function signup()
    {
        $misc = new MiscellaneousController();

        $language = $misc->getLanguage();

        $queryResult['seoInfo'] = $language->seoInfo()->select('meta_keywords_vendor_signup', 'meta_description_vendor_signup')->first();

        $queryResult['pageHeading'] = $misc->getPageHeading($language);

        $queryResult['recaptchaInfo'] = Basic::select('google_recaptcha_status')->first();

        $queryResult['bgImg'] = $misc->getBreadcrumb();

        return view('vendors.auth.register', $queryResult);
    }
    //create
    public function create(Request $request)
    {
        $admin = Admin::select('username')->first();
        $admin_username = $admin->username;
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'username' => "required|unique:vendors|not_in:$admin_username",
            'email' => 'required|email|unique:vendors',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($request->username == 'admin') {
            Session::flash('username_error', "You can not use $admin_username as a username!");
            return redirect()->back();
        }

        $in = $request->all();
        $setting = DB::table('basic_settings')->where('uniqid', 12345)->select('vendor_email_verification', 'vendor_admin_approval')->first();

        if ($setting->vendor_email_verification == 1) {
            // first, get the mail template information from db
            $mailTemplate = MailTemplate::where('mail_type', 'verify_email')->first();

            $mailSubject = $mailTemplate->mail_subject;
            $mailBody = $mailTemplate->mail_body;

            // second, send a password reset link to user via email
            $info = DB::table('basic_settings')
                ->select('website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
                ->first();

            $name = $request->username;
            $token =  $request->email;

            $link = '<a href=' . url("vendor/email/verify?token=" . $token) . '>Click Here</a>';

            $mailBody = str_replace('{username}', $name, $mailBody);
            $mailBody = str_replace('{verification_link}', $link, $mailBody);
            $mailBody = str_replace('{website_title}', $info->website_title, $mailBody);

            // initialize a new mail
            $mail = new PHPMailer(true);
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            // if smtp status == 1, then set some value for PHPMailer
            if ($info->smtp_status == 1) {

                $mail->isSMTP();
                $mail->Host       = $info->smtp_host;
                $mail->SMTPAuth   = true;
                $mail->Username   = $info->smtp_username;
                $mail->Password   = $info->smtp_password;

                if ($info->encryption == 'TLS') {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                }

                $mail->Port       = $info->smtp_port;
            }

            // finally add other informations and send the mail
            try {
                $mail->setFrom($info->from_mail, $info->from_name);
                $mail->addAddress($request->email);

                $mail->isHTML(true);
                $mail->Subject = $mailSubject;
                $mail->Body = $mailBody;

                $mail = $mail->send();

                Session::flash('success', ' Verification mail has been sent to your email address!');
            } catch (\Exception $e) {
                Session::flash('error', 'Mail could not be sent!');
                return redirect()->back();
            }

            $in['status'] = 0;
        } else {
            Session::flash('success', 'Sign up successfully completed.Please Login Now');
        }
        if ($setting->vendor_admin_approval == 1) {
            $in['status'] = 0;
        }

        if ($setting->vendor_admin_approval == 0 && $setting->vendor_email_verification == 0) {
            $in['status'] = 1;
        }

        $in['password'] = Hash::make($request->password);
        $vendor = Vendor::create($in);

        $misc = new MiscellaneousController();

        $language = $misc->getLanguage();
        $in['language_id'] = $language->id;

        $in['vendor_id'] = $vendor->id;
        VendorInfo::create($in);

        return redirect()->route('vendor.login');
    }

    //login
    public function login()
    {
        $misc = new MiscellaneousController();

        $language = $misc->getLanguage();

        $queryResult['seoInfo'] = $language->seoInfo()->select('meta_keywords_vendor_login', 'meta_description_vendor_login')->first();

        $queryResult['pageHeading'] = $misc->getPageHeading($language);

        $queryResult['bgImg'] = $misc->getBreadcrumb();

        $queryResult['bs'] = Basic::query()->select('google_recaptcha_status', 'facebook_login_status', 'google_login_status')->first();
        return view('vendors.auth.login', $queryResult);
    }

    //authenticate
    public function authentication(Request $request)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);


        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        if (
            Auth::guard('vendor')->attempt([
                'email' => $request->email,
                'password' => $request->password
            ])
        ) {
            $authAdmin = Auth::guard('vendor')->user();

            $setting = DB::table('basic_settings')->where('uniqid', 12345)->select('vendor_email_verification', 'vendor_admin_approval')->first();

            // check whether the admin's account is active or not
            if ($setting->vendor_email_verification == 1 && $authAdmin->email_verified_at == NULL && $authAdmin->status == 0) {
                Session::flash('error', 'Please Verify Your Email Address!');

                // logout auth admin as condition not satisfied
                Auth::guard('vendor')->logout();

                return redirect()->back();
            } elseif ($setting->vendor_email_verification == 0 && $setting->vendor_admin_approval == 1) {
                Session::put('secret_login', 0);
                return redirect()->route('vendor.dashboard');
            } else {
                Session::put('secret_login', 0);
                return redirect()->route('vendor.dashboard');
            }
        } else {
            return redirect()->back()->with('error', 'Oops, Email or password does not match!');
        }
    }
    //confirm_email'
    public function confirm_email()
    {
        $email = request()->input('token');
        $user = Vendor::where('email', $email)->first();
        $user->email_verified_at = now();
        $setting = DB::table('basic_settings')->where('uniqid', 12345)->select('vendor_admin_approval')->first();
        if ($setting->vendor_admin_approval != 1) {
            $user->status = 1;
        }

        $user->save();
        Auth::guard('vendor')->login($user);
        Session::put('secret_login', 0);
        return redirect()->intended('vendor/dashboard');
    }
    public function logout(Request $request)
    {
        Auth::guard('vendor')->logout();

        Session::forget('secret_login');

        return redirect()->route('vendor.login');
    }

    public function dashboard()
    {
        $information['totalEquipment'] = Equipment::query()->where('vendor_id', Auth::guard('vendor')->user()->id)->count();
        $information['totalBooking'] = EquipmentBooking::query()->where('vendor_id', Auth::guard('vendor')->user()->id)->count();
        $information['transcations'] = Transcation::where('vendor_id', Auth::guard('vendor')->user()->id)->orderBy('id', 'desc')->get()->count();

        $monthWiseTotalBookings = DB::table('equipment_bookings')
            ->select(DB::raw('month(created_at) as month'), DB::raw('count(id) as total_booking'))
            ->where('payment_status', '=', 'completed')
            ->where('vendor_id', Auth::guard('vendor')->user()->id)
            ->groupBy('month')
            ->whereYear('created_at', '=', date('Y'))
            ->get();

        $monthWiseTotalIncomes = DB::table('equipment_bookings')
            ->select(DB::raw('month(created_at) as month'), DB::raw('sum(received_amount) as total'))
            ->where('payment_status', '=', 'completed')
            ->where('vendor_id', Auth::guard('vendor')->user()->id)
            ->groupBy('month')
            ->whereYear('created_at', '=', date('Y'))
            ->get();

        $months = [];
        $bookings = [];
        $incomes = [];

        for ($i = 1; $i <= 12; $i++) {
            // get all 12 months name
            $monthNum = $i;
            $dateObj = DateTime::createFromFormat('!m', $monthNum);
            $monthName = $dateObj->format('M');
            array_push($months, $monthName);

            // get all 12 months's equipment booking
            $bookingFound = false;

            foreach ($monthWiseTotalBookings as $bookingInfo) {
                if ($bookingInfo->month == $i) {
                    $bookingFound = true;
                    array_push($bookings, $bookingInfo->total_booking);
                    break;
                }
            }

            if ($bookingFound == false) {
                array_push($bookings, 0);
            }

            // get all 12 months's income of equipment booking
            $incomeFound = false;

            foreach ($monthWiseTotalIncomes as $incomeInfo) {
                if ($incomeInfo->month == $i) {
                    $incomeFound = true;
                    array_push($incomes, $incomeInfo->total);
                    break;
                }
            }

            if ($incomeFound == false) {
                array_push($incomes, 0);
            }
        }



        $information['months'] = $months;
        $information['bookings'] = $bookings;
        $information['incomes'] = $incomes;

        $information['admin_setting'] = DB::table('basic_settings')->where('uniqid', 12345)->select('vendor_admin_approval', 'admin_approval_notice')->first();

        return view('vendors.index', $information);
    }

    //change_password
    public function change_password()
    {
        return view('vendors.auth.change-password');
    }

    //update_password
    public function updated_password(Request $request)
    {
        $rules = [
            'current_password' => [
                'required',
                new MatchOldPasswordRule('vendor')

            ],
            'new_password' => 'required|confirmed',
            'new_password_confirmation' => 'required'
        ];

        $messages = [
            'new_password.confirmed' => 'Password confirmation does not match.',
            'new_password_confirmation.required' => 'The confirm new password field is required.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        }

        $vendor = Auth::guard('vendor')->user();

        $vendor->update([
            'password' => Hash::make($request->new_password)
        ]);

        Session::flash('success', 'Password updated successfully!');

        return response()->json(['status' => 'success'], 200);
    }

    //edit_profile
    public function edit_profile(Request $request)
    {
        $language = Language::where('code', $request->language)->first();
        $information['languages'] = Language::all();

        $vendor_id = Auth::guard('vendor')->user()->id;
        $vendor = Vendor::where('id', $vendor_id)->with([
            'vendor_info' => function ($query) use ($language) {
                return $query->where('language_id', $language->id);
            }
        ])->first();
        $information['vendor'] = $vendor;
        $information['language'] = $language;
        return view('vendors.auth.edit-profile', $information);
    }
    //update_profile
    public function update_profile(Request $request, Vendor $vendor)
    {
        $id = Auth::guard('vendor')->user()->id;
        $rules = [

            'username' => [
                'required',
                'not_in:admin',
                Rule::unique('vendors', 'username')->ignore($id),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('vendors', 'email')->ignore($id)
            ]
        ];

        if ($request->hasFile('photo')) {
            $rules['photo'] = 'mimes:png,jpeg,jpg|dimensions:min_width=80,max_width=80,min_width=80,min_height=80';
        }

        $languages = Language::get();
        foreach ($languages as $language) {
            $rules[$language->code . '_name'] = 'required';
            $rules[$language->code . '_shop_name'] = 'required';
        }

        $messages = [];

        foreach ($languages as $language) {
            $messages[$language->code . '_name.required'] = 'The Name field is required.';

            $messages[$language->code . '_shop_name.required'] = 'The shop name field is required.';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }


        $in = $request->all();
        $vendor  = Vendor::where('id', $id)->first();
        $file = $request->file('photo');
        if ($file) {
            $extension = $file->getClientOriginalExtension();
            $directory = public_path('assets/admin/img/vendor-photo/');
            $fileName = uniqid() . '.' . $extension;
            @mkdir($directory, 0775, true);
            $file->move($directory, $fileName);

            @unlink(public_path('assets/admin/img/vendor-photo/') . $vendor->photo);
            $in['photo'] = $fileName;
        }


        if ($request->show_email_addresss) {
            $in['show_email_addresss'] = 1;
        } else {
            $in['show_email_addresss'] = 0;
        }
        if ($request->show_phone_number) {
            $in['show_phone_number'] = 1;
        } else {
            $in['show_phone_number'] = 0;
        }
        if ($request->show_contact_form) {
            $in['show_contact_form'] = 1;
        } else {
            $in['show_contact_form'] = 0;
        }



        $vendor->update($in);

        $languages = Language::get();
        $vendor_id = $vendor->id;
        foreach ($languages as $language) {
            $vendorInfo = VendorInfo::where('vendor_id', $vendor_id)->where('language_id', $language->id)->first();
            if ($vendorInfo == NULL) {
                $vendorInfo = new VendorInfo();
            }
            $vendorInfo->language_id = $language->id;
            $vendorInfo->vendor_id = $vendor_id;
            $vendorInfo->name = $request[$language->code . '_name'];
            $vendorInfo->shop_name = $request[$language->code . '_shop_name'];
            $vendorInfo->country = $request[$language->code . '_country'];
            $vendorInfo->city = $request[$language->code . '_city'];
            $vendorInfo->state = $request[$language->code . '_state'];
            $vendorInfo->zip_code = $request[$language->code . '_zip_code'];
            $vendorInfo->address = $request[$language->code . '_address'];
            $vendorInfo->details = $request[$language->code . '_details'];
            $vendorInfo->save();
        }



        Session::flash('success', 'Vendor updated successfully!');

        return Response::json(['status' => 'success'], 200);
    }

    public function changeTheme(Request $request)
    {
        Session::put('vendor_theme_version', $request->vendor_theme_version);
        return redirect()->back();
    }

    //transcation 
    public function transcation(Request $request)
    {
        $transcation_id = null;
        if ($request->filled('transcation_id')) {
            $transcation_id = $request->transcation_id;
        }

        $transcations = Transcation::where('vendor_id', Auth::guard('vendor')->user()->id)
            ->when($transcation_id, function ($query) use ($transcation_id) {
                return $query->where('transcation_id', 'like', '%' . $transcation_id . '%');
            })
            ->orderBy('id', 'desc')->paginate(10);
        return view('vendors.transcation', compact('transcations'));
    }

    //destroy
    public function destroy(Request $request)
    {
        $transcation = Transcation::findOrFail($request->id);
        $transcation->delete();
        Session::flash('success', 'Transcation Deleted successfully!');

        return back();
    }

    //destroy
    public function bulk_destroy(Request $request)
    {
        $ids = $request->ids;
        foreach ($ids as $id) {
            $transcation = Transcation::findOrFail($id);
            $transcation->delete();
        }
        Session::flash('success', 'Transcation Deleted successfully!');

        return response()->json(['status' => 'success'], 200);
    }


    //forget_passord
    public function forget_passord()
    {
        $misc = new MiscellaneousController();

        $language = $misc->getLanguage();

        $queryResult['seoInfo'] = $language->seoInfo()->select('meta_keywords_vendor_forget_password', 'meta_descriptions_vendor_forget_password')->first();

        $queryResult['pageHeading'] = $misc->getPageHeading($language);

        $queryResult['bgImg'] = $misc->getBreadcrumb();
        return view('vendors.auth.forget-password', $queryResult);
    }
    //forget_mail
    public function forget_mail(Request $request)
    {
        $rules = [
            'email' => [
                'required',
                'email:rfc,dns',
                new MatchEmailRule('vendor')
            ]
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Vendor::where('email', $request->email)->first();

        // first, get the mail template information from db
        $mailTemplate = MailTemplate::where('mail_type', 'reset_password')->first();
        $mailSubject = $mailTemplate->mail_subject;
        $mailBody = $mailTemplate->mail_body;

        // second, send a password reset link to user via email
        $info = DB::table('basic_settings')
            ->select('website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
            ->first();

        $name = $user->username;
        $token =  Str::random(32);
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => $token,
        ]);

        $link = '<a href=' . url("vendor/reset-password?token=" . $token) . '>Click Here</a>';

        $mailBody = str_replace('{customer_name}', $name, $mailBody);
        $mailBody = str_replace('{password_reset_link}', $link, $mailBody);
        $mailBody = str_replace('{website_title}', $info->website_title, $mailBody);

        // initialize a new mail
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        // if smtp status == 1, then set some value for PHPMailer
        if ($info->smtp_status == 1) {
            $mail->isSMTP();
            $mail->Host       = $info->smtp_host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $info->smtp_username;
            $mail->Password   = $info->smtp_password;

            if ($info->encryption == 'TLS') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mail->Port       = $info->smtp_port;
        }

        // finally add other informations and send the mail
        try {
            $mail->setFrom($info->from_mail, $info->from_name);
            $mail->addAddress($request->email);

            $mail->isHTML(true);
            $mail->Subject = $mailSubject;
            $mail->Body = $mailBody;

            $mail->send();

            Session::flash('success', 'A mail has been sent to your email address.');
        } catch (\Exception $e) {
            Session::flash('error', 'Mail could not be sent!');
        }

        // store user email in session to use it later
        $request->session()->put('userEmail', $user->email);

        return redirect()->back();
    }
    //reset_password
    public function reset_password()
    {
        $misc = new MiscellaneousController();

        $language = $misc->getLanguage();

        $queryResult['seoInfo'] = $language->seoInfo()->select('meta_keywords_vendor_forget_password', 'meta_descriptions_vendor_forget_password')->first();

        $queryResult['bgImg'] = $misc->getBreadcrumb();
        return view('vendors.auth.reset-password', $queryResult);
    }
    //update_password
    public function update_password(Request $request)
    {
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
            return redirect()->back()->withErrors($validator);
        }

        $reset = DB::table('password_resets')->where('token', $request->token)->first();
        $email = $reset->email;

        $vendor = Vendor::where('email',  $email)->first();

        $vendor->update([
            'password' => Hash::make($request->new_password)
        ]);
        DB::table('password_resets')->where('token', $request->token)->delete();
        Session::flash('success', 'Reset Your Password Successfully Completed.Please Login Now');

        return redirect()->route('vendor.login');
    }

    public function methodSettings()
    {
        $data = Vendor::where('id', Auth::guard('vendor')->user()->id)->select('self_pickup_status', 'two_way_delivery_status')->first();

        return view('vendors.shipping-methods', ['data' => $data]);
    }

    public function updateMethodSettings(Request $request)
    {
        $rules = [
            'self_pickup_status' => 'required|numeric',
            'two_way_delivery_status' => 'required|numeric'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $vendor = Vendor::where('id', Auth::guard('vendor')->user()->id)->first();
        $vendor->self_pickup_status = $request->self_pickup_status;
        $vendor->two_way_delivery_status = $request->two_way_delivery_status;
        $vendor->save();

        Session('success', 'Settings updated successfully!');

        return redirect()->back();
    }

    //monthly  income
    public function monthly_income(Request $request)
    {
        if ($request->filled('year')) {
            $date = $request->input('year');
        } else {
            $date = date('Y');
        }

        $monthWiseTotalIncomes = DB::table('transcations')->where('vendor_id', Auth::guard('vendor')->user()->id)
            ->select(DB::raw('month(created_at) as month'), DB::raw('sum(grand_total) as total'))
            ->where(function ($query) {
                return $query->where('transcation_type', 1)
                    ->orWhere('transcation_type', 3);
            })
            ->groupBy('month')
            ->whereYear('created_at', '=', $date)
            ->get();

        $monthWiseTotalExpenses = DB::table('transcations')->where('vendor_id', Auth::guard('vendor')->user()->id)
            ->select(DB::raw('month(created_at) as month'), DB::raw('sum(grand_total) as total'))
            ->where(function ($query) {
                return $query->where('transcation_type', 4);
            })
            ->groupBy('month')
            ->whereYear('created_at', '=', $date)
            ->get();

        $months = [];
        $incomes = [];
        $expenses = [];
        for ($i = 1; $i <= 12; $i++) {
            // get all 12 months name
            $monthNum = $i;
            $dateObj = DateTime::createFromFormat('!m', $monthNum);
            $monthName = $dateObj->format('M');
            array_push($months, $monthName);

            // get all 12 months's income of equipment booking
            $incomeFound = false;
            foreach ($monthWiseTotalIncomes as $incomeInfo) {
                if ($incomeInfo->month == $i) {
                    $incomeFound = true;
                    array_push($incomes, $incomeInfo->total);
                    break;
                }
            }
            if ($incomeFound == false) {
                array_push($incomes, 0);
            }

            // get all 12 months's expenses of equipment booking
            $expensesFound = false;
            foreach ($monthWiseTotalExpenses as $expensesInfo) {
                if ($expensesInfo->month == $i) {
                    $expensesFound = true;
                    array_push($expenses, $expensesInfo->total);
                    break;
                }
            }
            if ($expensesFound == false) {
                array_push($expenses, 0);
            }
        }
        $information['months'] = $months;
        $information['incomes'] = $incomes;
        $information['expenses'] = $expenses;

        return view('vendors.income', $information);
    }
}
