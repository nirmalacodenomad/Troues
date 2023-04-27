<?php

namespace App\Http\Controllers\FrontEnd\Shop;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\MiscellaneousController;
use App\Http\Controllers\FrontEnd\PaymentGateway\FlutterwaveController;
use App\Http\Controllers\FrontEnd\PaymentGateway\InstamojoController;
use App\Http\Controllers\FrontEnd\PaymentGateway\MercadoPagoController;
use App\Http\Controllers\FrontEnd\PaymentGateway\MollieController;
use App\Http\Controllers\FrontEnd\PaymentGateway\OfflineController;
use App\Http\Controllers\FrontEnd\PaymentGateway\PayPalController;
use App\Http\Controllers\FrontEnd\PaymentGateway\PaystackController;
use App\Http\Controllers\FrontEnd\PaymentGateway\PaytmController;
use App\Http\Controllers\FrontEnd\PaymentGateway\RazorpayController;
use App\Http\Controllers\FrontEnd\PaymentGateway\StripeController;
use App\Http\Helpers\BasicMailer;
use App\Http\Requests\Shop\PurchaseProcessRequest;
use App\Models\BasicSettings\Basic;
use App\Models\BasicSettings\MailTemplate;
use App\Models\Earning;
use App\Models\Shop\ProductOrder;
use App\Models\Shop\ProductPurchaseItem;
use App\Models\Shop\ShippingCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PDF;

class PurchaseProcessController extends Controller
{
  public function index(PurchaseProcessRequest $request)
  {
    if (!$request->exists('gateway')) {
      Session::flash('error', 'Please select a payment method.');

      return redirect()->back()->withInput();
    } else if ($request['gateway'] == 'paypal') {
      $paypal = new PayPalController();

      return $paypal->index($request, 'product purchase');
    } else if ($request['gateway'] == 'instamojo') {
      $instamojo = new InstamojoController();

      return $instamojo->index($request, 'product purchase');
    } else if ($request['gateway'] == 'paystack') {
      $paystack = new PaystackController();

      return $paystack->index($request, 'product purchase');
    } else if ($request['gateway'] == 'flutterwave') {
      $flutterwave = new FlutterwaveController();

      return $flutterwave->index($request, 'product purchase');
    } else if ($request['gateway'] == 'razorpay') {
      $razorpay = new RazorpayController();

      return $razorpay->index($request, 'product purchase');
    } else if ($request['gateway'] == 'mercadopago') {
      $mercadopago = new MercadoPagoController();

      return $mercadopago->index($request, 'product purchase');
    } else if ($request['gateway'] == 'mollie') {
      $mollie = new MollieController();

      return $mollie->index($request, 'product purchase');
    } else if ($request['gateway'] == 'stripe') {
      $stripe = new StripeController();

      return $stripe->index($request, 'product purchase');
    } else if ($request['gateway'] == 'paytm') {
      $paytm = new PaytmController();

      return $paytm->index($request, 'product purchase');
    } else {
      $offline = new OfflineController();

      return $offline->index($request, 'product purchase');
    }
  }

  public function calculation(Request $request, $products)
  {
    $total = 0.00;

    foreach ($products as $key => $item) {
      $price = floatval($item['price']);
      $total += $price;
    }

    if ($request->session()->has('discount')) {
      $discountVal = $request->session()->get('discount');
    }

    $discount = isset($discountVal) ? floatval($discountVal) : 0.00;
    $subtotal = $total - $discount;
    $chargeId = $request->exists('charge_id') ? $request['charge_id'] : null;

    if (!is_null($chargeId)) {
      $shippingCharge = ShippingCharge::query()->where('id', '=', $chargeId)->pluck('shipping_charge')->first();
    } else {
      $shippingCharge = 0.00;
    }

    $taxData = Basic::select('product_tax_amount')->first();

    $taxAmount = floatval($taxData->product_tax_amount);
    $calculatedTax = $subtotal * ($taxAmount / 100);
    $grandTotal = $subtotal + floatval($shippingCharge) + $calculatedTax;

    $calculatedData = array(
      'total' => $total,
      'discount' => $discount,
      'subtotal' => $subtotal,
      'shippingCharge' => $request->exists('charge_id') ? $shippingCharge : null,
      'tax' => $calculatedTax,
      'grandTotal' => $grandTotal
    );

    return $calculatedData;
  }

  public function storeData($productList, $arrData)
  {
    $orderInfo = ProductOrder::query()->create([
      'user_id' => Auth::guard('web')->check() == true ? Auth::guard('web')->user()->id : null,
      'order_number' => uniqid(),
      'billing_first_name' => $arrData['billingFirstName'],
      'billing_last_name' => $arrData['billingLastName'],
      'billing_email' => $arrData['billingEmail'],
      'billing_contact_number' => $arrData['billingContactNumber'],
      'billing_address' => $arrData['billingAddress'],
      'billing_city' => $arrData['billingCity'],
      'billing_state' => $arrData['billingState'],
      'billing_country' => $arrData['billingCountry'],
      'shipping_first_name' => $arrData['shippingFirstName'],
      'shipping_last_name' => $arrData['shippingLastName'],
      'shipping_email' => $arrData['shippingEmail'],
      'shipping_contact_number' => $arrData['shippingContactNumber'],
      'shipping_address' => $arrData['shippingAddress'],
      'shipping_city' => $arrData['shippingCity'],
      'shipping_state' => $arrData['shippingState'],
      'shipping_country' => $arrData['shippingCountry'],
      'total' => $arrData['total'],
      'discount' => $arrData['discount'],
      'product_shipping_charge_id' => $arrData['productShippingChargeId'],
      'shipping_cost' => $arrData['shippingCharge'],
      'tax' => $arrData['tax'],
      'grand_total' => $arrData['grandTotal'],
      'currency_text' => $arrData['currencyText'],
      'currency_text_position' => $arrData['currencyTextPosition'],
      'currency_symbol' => $arrData['currencySymbol'],
      'currency_symbol_position' => $arrData['currencySymbolPosition'],
      'payment_method' => $arrData['paymentMethod'],
      'gateway_type' => $arrData['gatewayType'],
      'payment_status' => $arrData['paymentStatus'],
      'order_status' => $arrData['orderStatus'],
      'attachment' => array_key_exists('attachment', $arrData) ? $arrData['attachment'] : null
    ]);

    foreach ($productList as $key => $item) {
      ProductPurchaseItem::create([
        'product_order_id' => $orderInfo->id,
        'product_id' => $key,
        'title' => $item['title'],
        'quantity' => intval($item['quantity'])
      ]);
    }

    return $orderInfo;
  }

  public function generateInvoice($orderInfo, $productList)
  {
    $fileName = $orderInfo->order_number . '.pdf';

    $data['orderInfo'] = $orderInfo;
    $data['productList'] = $productList;

    $directory = public_path('assets/file/invoices/product/');
    @mkdir($directory, 0775, true);

    $fileLocated = $directory . $fileName;

    $data['taxData'] = Basic::select('product_tax_amount')->first();

    PDF::loadView('frontend.shop.invoice', $data)->save($fileLocated);

    return $fileName;
  }

  public function prepareMail($orderInfo, $transaction_id)
  {
    // get the mail template info from db
    $mailTemplate = MailTemplate::query()->where('mail_type', '=', 'product_order')->first();
    $mailData['subject'] = $mailTemplate->mail_subject;
    $mailBody = $mailTemplate->mail_body;

    // get the website title info from db
    $info = Basic::select('website_title')->first();

    $customerName = $orderInfo->billing_first_name . ' ' . $orderInfo->billing_last_name;
    $orderNumber = $orderInfo->order_number;
    $websiteTitle = $info->website_title;

    if (Auth::guard('web')->check() == true) {
      $orderLink = '<p>Order Details: <a href=' . url("user/product-order/" . $orderInfo->id . "/details") . '>Click Here</a></p>';
    } else {
      $orderLink = '';
    }

    // replacing with actual data
    $mailBody = str_replace('{transaction_id}', $transaction_id, $mailBody);
    $mailBody = str_replace('{customer_name}', $customerName, $mailBody);
    $mailBody = str_replace('{order_number}', $orderNumber, $mailBody);
    $mailBody = str_replace('{website_title}', $websiteTitle, $mailBody);
    $mailBody = str_replace('{order_link}', $orderLink, $mailBody);

    $mailData['body'] = $mailBody;

    $mailData['recipient'] = $orderInfo->billing_email;

    $mailData['invoice'] = 'assets/file/invoices/product/' . $orderInfo->invoice;

    BasicMailer::sendMail($mailData);

    return;
  }

  public function complete($type = null)
  {
    $misc = new MiscellaneousController();

    $queryResult['bgImg'] = $misc->getBreadcrumb();

    $queryResult['purchaseType'] = $type;

    return view('frontend.payment.purchase-success', $queryResult);
  }

  public function cancel(Request $request)
  {
    Session::flash('error', 'Sorry, an error has occured!');

    return redirect()->route('shop.products');
  }
}
