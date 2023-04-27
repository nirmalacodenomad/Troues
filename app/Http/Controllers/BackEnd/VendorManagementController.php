<?php



namespace App\Http\Controllers\BackEnd;



use App\Http\Controllers\Controller;

use App\Models\Admin;

use App\Models\BasicSettings\Basic;

use App\Models\BasicSettings\MailTemplate;

use App\Models\Instrument\Equipment;

use App\Models\Language;

use App\Models\Transcation;

use App\Models\Vendor;

use App\Models\VendorInfo;

use Exception;

use Illuminate\Auth\Events\Validated;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Response;

use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\Validator;

use Illuminate\Validation\Rule;

use PHPMailer\PHPMailer\PHPMailer;



class VendorManagementController extends Controller

{

    public function settings()

    {

        $setting = DB::table('basic_settings')->where('uniqid', 12345)->select('vendor_email_verification', 'vendor_admin_approval', 'admin_approval_notice')->first();

        return view('backend.end-user.vendor.settings', compact('setting'));

    }

    //update_setting

    public function update_setting(Request $request)

    {

        if ($request->vendor_email_verification) {

            $vendor_email_verification = 1;

        } else {

            $vendor_email_verification = 0;

        }

        if ($request->vendor_admin_approval) {

            $vendor_admin_approval = 1;

        } else {

            $vendor_admin_approval = 0;

        }

        // finally, store the favicon into db

        DB::table('basic_settings')->updateOrInsert(

            ['uniqid' => 12345],

            [

                'vendor_email_verification' => $vendor_email_verification,

                'vendor_admin_approval' => $vendor_admin_approval,

                'admin_approval_notice' => $request->admin_approval_notice,

            ]

        );



        Session::flash('success', 'Update Settings Successfully!');

        return back();

    }

    public function index(Request $request)

    {

        $searchKey = null;



        if ($request->filled('info')) {

            $searchKey = $request['info'];

        }



        $vendors = Vendor::when($searchKey, function ($query, $searchKey) {

            return $query->where('username', 'like', '%' . $searchKey . '%')

                ->orWhere('email', 'like', '%' . $searchKey . '%');

        })

            ->orderBy('id', 'desc')

            ->paginate(10);





        return view('backend.end-user.vendor.index', compact('vendors'));

    }



    //add

    public function add(Request $request)

    {

        // first, get the language info from db

        $language = Language::query()->where('code', '=', $request->language)->first();

        $information['language'] = $language;

        $information['languages'] = Language::get();

        return view('backend.end-user.vendor.create', $information);

    }

    public function create(Request $request)

    {


        $admin = Admin::select('username')->first();
        $admin_username = $admin->username;

        $rules = [

            'username' => "required|unique:vendors|not_in:$admin_username",

            'email' => 'required|email',

            'password' => 'required|min:6',

        ];





        $languages = Language::get();

        foreach ($languages as $language) {

            $rules[$language->code . '_name'] = 'required';

            $rules[$language->code . '_shop_name'] = 'required';

        }







        $validator = Validator::make($request->all(), $rules);



        if ($validator->fails()) {

            return Response::json([

                'errors' => $validator->getMessageBag()

            ], 400);

        }



        $in = $request->all();

        $in['password'] = Hash::make($request->password);

        $in['status'] = 1;



        $file = $request->file('photo');

        if ($file) {

            $extension = $file->getClientOriginalExtension();

            $directory = public_path('assets/admin/img/vendor-photo/');

            $fileName = uniqid() . '.' . $extension;

            @mkdir($directory, 0775, true);

            $file->move($directory, $fileName);

            $in['photo'] = $fileName;

        }



        $vendor = Vendor::create($in);



        $vendor_id = $vendor->id;

        foreach ($languages as $language) {

            $vendorInfo = new VendorInfo();

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





        Session::flash('success', 'Add Vendor Successfully!');

        return Response::json(['status' => 'success'], 200);

    }



    public function show($id)

    {



        $information['langs'] = Language::all();



        $language = Language::where('code', request()->input('language'))->first();

        $information['language'] = $language;

        $vendor = Vendor::with([

            'vendor_info' => function ($query) use ($language) {

                return $query->where('language_id', $language->id);

            }

        ])->find($id);

        $information['vendor'] = $vendor;



        $information['langs'] = Language::all();

        $information['currencyInfo'] = $this->getCurrencyInfo();



        $information['allEquipment'] = Equipment::query()->where('vendor_id', $vendor->id)->join('equipment_contents', 'equipments.id', '=', 'equipment_contents.equipment_id')

            ->join('equipment_categories', 'equipment_categories.id', '=', 'equipment_contents.equipment_category_id')

            ->where('equipment_contents.language_id', '=', $language->id)

            ->select('equipments.id', 'equipments.thumbnail_image', 'equipments.quantity', 'equipment_contents.title', 'equipment_contents.slug', 'equipment_categories.name as categoryName', 'equipments.is_featured')

            ->orderByDesc('equipments.id')

            ->get();



        return view('backend.end-user.vendor.details', $information);

    }

    public function updateAccountStatus(Request $request, $id)

    {



        $user = Vendor::find($id);

        if ($request->account_status == 1) {

            $user->update(['status' => 1]);

        } else {

            $user->update(['status' => 0]);

        }

        Session::flash('success', 'Account status updated successfully!');



        return redirect()->back();

    }



    public function updateEmailStatus(Request $request, $id)

    {

        $vendor = Vendor::find($id);

        if ($request->email_status == 1) {

            $vendor->update(['email_verified_at' => now()]);

        } else {

            $vendor->update(['email_verified_at' => NULL]);

        }

        Session::flash('success', 'Email status updated successfully!');



        return redirect()->back();

    }

    public function changePassword($id)

    {

        $userInfo = Vendor::find($id);



        return view('backend.end-user.vendor.change-password', compact('userInfo'));

    }

    public function updatePassword(Request $request, $id)

    {

        $rules = [

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



        $user = Vendor::find($id);



        $user->update([

            'password' => Hash::make($request->new_password)

        ]);



        Session::flash('success', 'Password updated successfully!');



        return Response::json(['status' => 'success'], 200);

    }



    public function edit($id)

    {

        $information['languages'] = Language::get();

        $vendor = Vendor::find($id);

        $information['vendor'] = $vendor;

        $information['currencyInfo'] = $this->getCurrencyInfo();

        return view('backend.end-user.vendor.edit', $information);

    }



    //update

    public function update(Request $request, $id, Vendor $vendor)

    {

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

            $messages[$language->code . '_name.required'] = 'The name field is required.';



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



    public function update_vendor_balance(Request $request, $id, Vendor $vendor)

    {

        $vendor  = Vendor::where('id', $id)->first();

        $currency_info = Basic::select('base_currency_symbol_position', 'base_currency_symbol')

            ->first();

        //add or subtract vendor balance

        if ($request->amount_status && $request->amount_status == 1) {

            $amount = $vendor->amount + $request->amount;



            //store data to transcation table

            $transcation = Transcation::create([

                'transcation_id' => time(),

                'booking_id' => NULL,

                'transcation_type' => 3,

                'user_id' => NULL,

                'vendor_id' => $vendor->id,

                'payment_status' => 1,

                'payment_method' => NULL,

                'grand_total' => $request->amount,

                'pre_balance' => $vendor->amount,

                'after_balance' => $amount,

                'gateway_type' => NULL,

                'currency_symbol' => $currency_info->base_currency_symbol,

                'currency_symbol_position' => $currency_info->base_currency_symbol_position,

            ]);



            $vendor_new_amount = $amount;

        } else {

            $amount = $vendor->amount - $request->amount;

            //store data to transcation table

            $transcation = Transcation::create([

                'transcation_id' => time(),

                'booking_id' => NULL,

                'transcation_type' => 4,

                'user_id' => NULL,

                'vendor_id' => $vendor->id,

                'payment_status' => 1,

                'payment_method' => NULL,

                'grand_total' => $request->amount,

                'pre_balance' => $vendor->amount,

                'after_balance' => $amount,

                'gateway_type' => NULL,

                'currency_symbol' => $currency_info->base_currency_symbol,

                'currency_symbol_position' => $currency_info->base_currency_symbol_position,

            ]);



            $vendor_new_amount = $amount;

        }



        //send mail

        if ($request->amount_status == 1 || $request->amount_status == 0) {

            if ($request->amount_status == 1) {

                $template_type = 'balance_add';



                $vendor_alert_msg = "Balance added to vendor account succefully.!";

            } else {

                $template_type = 'balance_subtract';

                $vendor_alert_msg = "Balance Subtract from vendor account succefully.!";

            }

            //mail sending

            // get the website title & mail's smtp information from db

            $info = Basic::select('website_title', 'smtp_status', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name', 'base_currency_symbol_position', 'base_currency_symbol')

                ->first();



            //preparing mail info

            // get the mail template info from db

            $mailTemplate = MailTemplate::query()->where('mail_type', '=', $template_type)->first();

            $mailData['subject'] = $mailTemplate->mail_subject;

            $mailBody = $mailTemplate->mail_body;



            // get the website title info from db

            $website_info = Basic::select('website_title')->first();



            // preparing dynamic data

            $vendorName = $vendor->username;

            $vendorEmail = $vendor->email;

            $vendor_amount = $amount;



            $websiteTitle = $website_info->website_title;



            // replacing with actual data

            $mailBody = str_replace('{transaction_id}', $transcation->transcation_id, $mailBody);

            $mailBody = str_replace('{username}', $vendorName, $mailBody);

            $mailBody = str_replace('{amount}', $info->base_currency_symbol . $request->amount, $mailBody);



            $mailBody = str_replace('{current_balance}', $info->base_currency_symbol . $vendor_amount, $mailBody);

            $mailBody = str_replace('{website_title}', $websiteTitle, $mailBody);



            $mailData['body'] = $mailBody;



            $mailData['recipient'] = $vendorEmail;

            //preparing mail info end



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



            // add other informations and send the mail

            try {

                $mail->setFrom($info->from_mail, $info->from_name);

                $mail->addAddress($mailData['recipient']);



                $mail->isHTML(true);

                $mail->Subject = $mailData['subject'];

                $mail->Body = $mailData['body'];



                $mail->send();

                Session::flash('success', $vendor_alert_msg);

            } catch (Exception $e) {

                Session::flash('warning', 'Mail could not be sent. Mailer Error: ' . $mail->ErrorInfo);

            }

            //mail sending end

        }

        $vendor->amount = $vendor_new_amount;

        $vendor->save();

        return Response::json(['status' => 'success'], 200);

    }



    public function destroy($id)

    {

        $vendor = Vendor::find($id);



        /***********==Reveiew==*********** */

        $reviews = $vendor->reviews()->get();

        foreach ($reviews as $review) {

            $review->delete();

        }

        /*********************************************/

        #============delete vendor equipment==========



        $equipments = $vendor->equipment()->get();



        foreach ($equipments as $equipment) {

            // delete the thumbnail image

            @unlink(public_path('assets/img/equipments/thumbnail-images/') . $equipment->thumbnail_image);



            // delete the slider images

            $sliderImages = json_decode($equipment->slider_images);



            foreach ($sliderImages as $sliderImage) {

                @unlink(public_path('assets/img/equipments/slider-images/') . $sliderImage);

            }



            // delete the equipment contents

            $equipmentContents = $equipment->content()->get();



            foreach ($equipmentContents as $equipmentContent) {

                $equipmentContent->delete();

            }



            // delete all the bookings of this equipment

            $bookings = $equipment->booking()->get();



            if (count($bookings) > 0) {

                foreach ($bookings as $booking) {

                    @unlink(public_path('assets/file/attachments/equipment/') . $booking->attachment);



                    @unlink(public_path('assets/file/invoices/equipment/') . $booking->invoice);



                    $booking->delete();

                }

            }



            // delete all the reviews of this equipment

            $equipmentReviews = $equipment->review()->get();



            if (count($equipmentReviews) > 0) {

                foreach ($equipmentReviews as $equipmentReview) {

                    $equipmentReview->delete();

                }

            }



            //delete all support ticket

            $support_tickets = $vendor->support_ticket()->get();

            if (count($support_tickets) > 0) {

                foreach ($support_tickets as $support_ticket) {

                    //delete conversation 

                    $messages = $support_ticket->messages()->get();

                    foreach ($messages as $message) {

                        @unlink(public_path('assets/admin/img/support-ticket/') . $message->file);

                        $message->delete();

                    }

                    @unlink(public_path('assets/admin/img/support-ticket/attachment/') . $support_ticket->attachment);

                    $support_ticket->delete();

                }

            }



            $equipment->delete();

        }

        /*********************************************/

        #====finally delete the vendor=======

        @unlink(public_path('assets/admin/img/vendor-photo/') . $vendor->photo);



        $vendorInfos = $vendor->vendor_info()->get();

        foreach ($vendorInfos as $item) {

            $item->delete();

        }

        $vendor->delete();



        return redirect()->back()->with('success', 'Vendor info deleted successfully!');

    }



    public function bulkDestroy(Request $request)

    {

        $ids = $request->ids;



        foreach ($ids as $id) {

            $vendor = Vendor::find($id);

            /***********==Reveiew==*********** */

            $reviews = $vendor->reviews()->get();

            foreach ($reviews as $review) {

                $review->delete();

            }

            /*********************************************/

            //============delete vendor equipment==========



            $equipments = $vendor->equipment()->get();



            foreach ($equipments as $equipment) {

                // delete the thumbnail image

                @unlink(public_path('assets/img/equipments/thumbnail-images/') . $equipment->thumbnail_image);



                // delete the slider images

                $sliderImages = json_decode($equipment->slider_images);



                foreach ($sliderImages as $sliderImage) {

                    @unlink(public_path('assets/img/equipments/slider-images/') . $sliderImage);

                }



                // delete the equipment contents

                $equipmentContents = $equipment->content()->get();



                foreach ($equipmentContents as $equipmentContent) {

                    $equipmentContent->delete();

                }



                // delete all the bookings of this equipment

                $bookings = $equipment->booking()->get();



                if (count($bookings) > 0) {

                    foreach ($bookings as $booking) {

                        @unlink(public_path('assets/file/attachments/equipment/') . $booking->attachment);



                        @unlink(public_path('assets/file/invoices/equipment/') . $booking->invoice);



                        $booking->delete();

                    }

                }



                // delete all the reviews of this equipment

                $equipmentReviews = $equipment->review()->get();



                if (count($equipmentReviews) > 0) {

                    foreach ($equipmentReviews as $equipmentReview) {

                        $equipmentReview->delete();

                    }

                }



                $equipment->delete();

            }

            /*********************************************/



            $vendorInfos = $vendor->vendor_info()->get();

            foreach ($vendorInfos as $item) {

                $item->delete();

            }



            #====finally delete the vendor=======

            @unlink(public_path('assets/admin/img/vendor-photo/') . $vendor->photo);

            $vendor->delete();

        }



        Session::flash('success', 'Vendors info deleted successfully!');



        return Response::json(['status' => 'success'], 200);

    }



    //secrtet login

    public function secret_login($id)

    {

        Session::put('secret_login', 1);

        $vendor = Vendor::where('id', $id)->first();

        Auth::guard('vendor')->login($vendor);

        return redirect()->route('vendor.dashboard');

    }

}

