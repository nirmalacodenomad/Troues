<?php

namespace App\Http\Controllers\FrontEnd\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Instrument\BookingProcessController;
use App\Http\Controllers\FrontEnd\Shop\PurchaseProcessController;
use App\Models\Commission;
use App\Models\Earning;
use App\Models\Instrument\Equipment;
use App\Models\PaymentGateway\OnlineGateway;
use App\Models\Shop\Product;
use App\Models\Transcation;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class PayPalController extends Controller
{
  private $api_context;

  public function __construct()
  {
    $data = OnlineGateway::whereKeyword('paypal')->first();
    $paypalData = json_decode($data->information, true);

    $paypal_conf = Config::get('paypal');
    $paypal_conf['client_id'] = $paypalData['client_id'];
    $paypal_conf['secret'] = $paypalData['client_secret'];
    $paypal_conf['settings']['mode'] = $paypalData['sandbox_status'] == 1 ? 'sandbox' : 'live';

    $this->api_context = new ApiContext(
      new OAuthTokenCredential(
        $paypal_conf['client_id'],
        $paypal_conf['secret']
      )
    );

    $this->api_context->setConfig($paypal_conf['settings']);
  }

  public function index(Request $request, $paymentFor)
  {
    if ($paymentFor == 'product purchase') {
      // get the products from session
      if ($request->session()->has('productCart')) {
        $productList = $request->session()->get('productCart');
      } else {
        Session::flash('error', 'Something went wrong!');

        return redirect()->route('shop.products');
      }

      $purchaseProcess = new PurchaseProcessController();

      // do calculation
      $calculatedData = $purchaseProcess->calculation($request, $productList);
    } else if ($paymentFor == 'equipment booking') {
      // check whether the equipment lowest price exist or not in session
      if (!$request->session()->has('totalPrice')) {
        Session::flash('error', 'Something went wrong!');

        return redirect()->route('all_equipment');
      }

      $bookingProcess = new BookingProcessController();

      // do calculation
      $calculatedData = $bookingProcess->calculation($request);
    }

    $currencyInfo = $this->getCurrencyInfo();

    // changing the currency before redirect to PayPal
    if ($currencyInfo->base_currency_text !== 'USD') {
      $rate = floatval($currencyInfo->base_currency_rate);
      $convertedTotal = $calculatedData['grandTotal'] / $rate;
    }

    $paypalTotal = $currencyInfo->base_currency_text === 'USD' ? $calculatedData['grandTotal'] : $convertedTotal;

    if ($paymentFor == 'product purchase') {
      $arrData = array(
        'billingFirstName' => $request['billing_first_name'],
        'billingLastName' => $request['billing_last_name'],
        'billingEmail' => $request['billing_email'],
        'billingContactNumber' => $request['billing_contact_number'],
        'billingAddress' => $request['billing_address'],
        'billingCity' => $request['billing_city'],
        'billingState' => $request['billing_state'],
        'billingCountry' => $request['billing_country'],
        'shippingFirstName' => $request['shipping_first_name'],
        'shippingLastName' => $request['shipping_last_name'],
        'shippingEmail' => $request['shipping_email'],
        'shippingContactNumber' => $request['shipping_contact_number'],
        'shippingAddress' => $request['shipping_address'],
        'shippingCity' => $request['shipping_city'],
        'shippingState' => $request['shipping_state'],
        'shippingCountry' => $request['shipping_country'],
        'total' => $calculatedData['total'],
        'discount' => $calculatedData['discount'],
        'productShippingChargeId' => $request->exists('charge_id') ? $request['charge_id'] : null,
        'shippingCharge' => $calculatedData['shippingCharge'],
        'tax' => $calculatedData['tax'],
        'grandTotal' => $calculatedData['grandTotal'],
        'currencyText' => $currencyInfo->base_currency_text,
        'currencyTextPosition' => $currencyInfo->base_currency_text_position,
        'currencySymbol' => $currencyInfo->base_currency_symbol,
        'currencySymbolPosition' => $currencyInfo->base_currency_symbol_position,
        'paymentMethod' => 'PayPal',
        'gatewayType' => 'online',
        'paymentStatus' => 'completed',
        'orderStatus' => 'pending'
      );

      $title = 'Purchase Product';
      $notifyURL = route('shop.purchase_product.paypal.notify');
      $cancelURL = route('shop.purchase_product.cancel');
    } else if ($paymentFor == 'equipment booking') {
      // get start & end date
      $dates = $bookingProcess->getDates($request['dates']);

      // get location name
      $location_name = $bookingProcess->getLocation($request['location']);

      $arrData = array(
        'name' => $request['name'],
        'contactNumber' => $request['contact_number'],
        'email' => $request['email'],
        'equipmentId' => $request['equipment_id'],
        'startDate' => $dates['startDate'],
        'endDate' => $dates['endDate'],
        'shippingMethod' => $request->filled('shipping_method') ? $request['shipping_method'] : null,
        'location' => $location_name,
        'total' => $calculatedData['total'],
        'discount' => $calculatedData['discount'],
        'shippingCost' => $calculatedData['shippingCharge'],
        'tax' => $calculatedData['tax'],
        'grandTotal' => $calculatedData['grandTotal'],
        'currencySymbol' => $currencyInfo->base_currency_symbol,
        'currencySymbolPosition' => $currencyInfo->base_currency_symbol_position,
        'currencyText' => $currencyInfo->base_currency_text,
        'currencyTextPosition' => $currencyInfo->base_currency_text_position,
        'paymentMethod' => 'PayPal',
        'gatewayType' => 'online',
        'paymentStatus' => 'completed',
        'shippingStatus' => !$request->filled('shipping_method') ? null : 'pending'
      );

      $title = 'Equipment Booking';
      $notifyURL = route('equipment.make_booking.paypal.notify');
      $cancelURL = route('equipment.make_booking.cancel');
    }

    $payer = new Payer();
    $payer->setPaymentMethod('paypal');
    $item_1 = new Item();
    $item_1->setName($title)
      /** item name **/
      ->setCurrency('USD')
      ->setQuantity(1)
      ->setPrice($paypalTotal);
    /** unit price **/
    $item_list = new ItemList();
    $item_list->setItems(array($item_1));
    $amount = new Amount();
    $amount->setCurrency('USD')
      ->setTotal($paypalTotal);
    $transaction = new Transaction();
    $transaction->setAmount($amount)
      ->setItemList($item_list)
      ->setDescription($title . ' via PayPal');
    $redirect_urls = new RedirectUrls();
    $redirect_urls->setReturnUrl($notifyURL)
      /** Specify return URL **/
      ->setCancelUrl($cancelURL);
    $payment = new Payment();
    $payment->setIntent('Sale')
      ->setPayer($payer)
      ->setRedirectUrls($redirect_urls)
      ->setTransactions(array($transaction));

    try {
      $payment->create($this->api_context);
    } catch (\PayPal\Exception\PPConnectionException $ex) {
      return redirect($cancelURL)->with('error', $ex->getMessage());
    }

    foreach ($payment->getLinks() as $link) {
      if ($link->getRel() == 'approval_url') {
        $redirectURL = $link->getHref();
        break;
      }
    }

    // put some data in session before redirect to paypal url
    $request->session()->put('paymentFor', $paymentFor);
    $request->session()->put('arrData', $arrData);
    $request->session()->put('paymentId', $payment->getId());

    if (isset($redirectURL)) {
      /** redirect to paypal **/
      return Redirect::away($redirectURL);
    }
  }

  public function notify(Request $request)
  {
    // get the information from session
    $paymentPurpose = $request->session()->get('paymentFor');

    if ($paymentPurpose == 'product purchase') {
      $productList = $request->session()->get('productCart');
    }

    $arrData = $request->session()->get('arrData');
    $paymentId = $request->session()->get('paymentId');

    $urlInfo = $request->all();

    if (empty($urlInfo['token']) || empty($urlInfo['PayerID'])) {
      if ($paymentPurpose == 'product purchase') {
        return redirect()->route('shop.purchase_product.cancel');
      } else if ($paymentPurpose == 'equipment booking') {
        return redirect()->route('equipment.make_booking.cancel');
      }
    }

    /** Execute The Payment **/
    $payment = Payment::get($paymentId, $this->api_context);
    $execution = new PaymentExecution();
    $execution->setPayerId($urlInfo['PayerID']);
    $result = $payment->execute($execution, $this->api_context);

    if ($result->getState() == 'approved') {
      // remove this session datas
      $request->session()->forget('paymentFor');
      $request->session()->forget('arrData');
      $request->session()->forget('paymentId');

      if ($paymentPurpose == 'product purchase') {
        $purchaseProcess = new PurchaseProcessController();

        // store product order information in database
        $orderInfo = $purchaseProcess->storeData($productList, $arrData);

        //add blance to admin revinue
        $earning = Earning::first();

        $earning->total_revenue = $earning->total_revenue + $orderInfo->grand_total;
        $earning->total_earning = $earning->total_earning + $orderInfo->grand_total;
        $earning->save();
        
        $transactionStoreArr = [
          'transcation_id' => time(),
          'booking_id' => $orderInfo->id,
          'transcation_type' => 5,
          'user_id' => null,
          'vendor_id' => NULL,
          'payment_status' => 1,
          'payment_method' => $orderInfo->gateway_type,
          'grand_total' => $orderInfo->grand_total,
          'pre_balance' => NULL,
          'after_balance' => NULL,
          'gateway_type' => $orderInfo->gateway_type,
          'currency_symbol' => $orderInfo->currency_symbol,
          'currency_symbol_position' => $orderInfo->currency_symbol_position,
        ];

        storeTranscation($transactionStoreArr);

        // then subtract each product quantity from respective product stock
        foreach ($productList as $key => $item) {
          $product = Product::query()->find($key);

          if ($product->product_type == 'physical') {
            $stock = $product->stock - intval($item['quantity']);

            $product->update(['stock' => $stock]);
          }
        }

        // generate an invoice in pdf format
        $invoice = $purchaseProcess->generateInvoice($orderInfo, $productList);

        // then, update the invoice field info in database
        $orderInfo->update(['invoice' => $invoice]);

        // send a mail to the customer with the invoice
        $purchaseProcess->prepareMail($orderInfo, $transactionStoreArr['transcation_id']);

        // remove all session data
        $request->session()->forget('productCart');
        $request->session()->forget('discount');

        return redirect()->route('shop.purchase_product.complete');
      } else if ($paymentPurpose == 'equipment booking') {
        $bookingProcess = new BookingProcessController();

        // store equipment booking information in database
        $bookingInfo = $bookingProcess->storeData($arrData);

        // generate an invoice in pdf format
        $invoice = $bookingProcess->generateInvoice($bookingInfo);
        //calculate commission start

        $equipment = Equipment::findOrFail($arrData['equipmentId']);
        if (!empty($equipment)) {
          if ($equipment->vendor_id != NULL) {
            $vendor_id = $equipment->vendor_id;
          } else {
            $vendor_id = NULL;
          }
        } else {
          $vendor_id = NULL;
        }
        //calculate commission start

        $equipment = Equipment::findOrFail($arrData['equipmentId']);
        if (!empty($equipment)) {
          if ($equipment->vendor_id != NULL) {
            $vendor_id = $equipment->vendor_id;
          } else {
            $vendor_id = NULL;
          }
        } else {
          $vendor_id = NULL;
        }
        //calculate commission
        $percent = Commission::select('equipment_commission')->first();

        $commission = (($bookingInfo->total - $bookingInfo->discount) * $percent->equipment_commission) / 100;

        //get vendor
        $vendor = Vendor::where('id', $bookingInfo->vendor_id)->first();


        //add blance to admin revinue
        $earning = Earning::first();

        $earning->total_revenue = $earning->total_revenue + $bookingInfo->grand_total;
        if ($vendor) {
          $earning->total_earning = $earning->total_earning + $commission + $bookingInfo->tax;
        } else {
          $earning->total_earning = $earning->total_earning + $bookingInfo->grand_total;
        }
        $earning->save();


        //store Balance  to vendor
        if ($vendor) {
          $pre_balance = $vendor->amount;
          $vendor->amount = $vendor->amount + ($bookingInfo->grand_total - ($commission + $bookingInfo->tax));
          $vendor->save();
          $after_balance = $vendor->amount;

          $received_amount = ($bookingInfo->grand_total - ($commission + $bookingInfo->tax));

          // then, update the invoice field info in database
          $bookingInfo->update([
            'invoice' => $invoice,
            'comission' => $commission,
            'received_amount' => $received_amount,
          ]);
        } else {
          // then, update the invoice field info in database
          $bookingInfo->update([
            'invoice' => $invoice
          ]);
          $received_amount = $bookingInfo->grand_total;
          $after_balance = NULL;
          $pre_balance = NULL;
        }
        //calculate commission end

        //store data to transcation table
        $transactionStoreArr = [
          'transcation_id' => time(),
          'booking_id' => $bookingInfo->id,
          'transcation_type' => 1,
          'user_id' => Auth::guard('web')->check() == true ? Auth::guard('web')->user()->id : null,
          'vendor_id' => $vendor_id,
          'payment_status' => 1,
          'payment_method' => $bookingInfo->payment_method,
          'shipping_charge' => $bookingInfo->shipping_cost,
          'commission' => $bookingInfo->comission,
          'tax' => $bookingInfo->tax,
          'grand_total' => $received_amount,
          'pre_balance' => $pre_balance,
          'after_balance' => $after_balance,
          'gateway_type' => $bookingInfo->gateway_type,
          'currency_symbol' => $bookingInfo->currency_symbol,
          'currency_symbol_position' => $bookingInfo->currency_symbol_position,
        ];

        storeTranscation($transactionStoreArr);

        // send a mail to the customer with the invoice
        $bookingProcess->prepareMail($bookingInfo, $transactionStoreArr['transcation_id']);



        // remove all session data
        $request->session()->forget('totalPrice');
        $request->session()->forget('equipmentDiscount');

        return redirect()->route('equipment.make_booking.complete');
      }
    } else {
      $request->session()->forget('paymentFor');
      $request->session()->forget('arrData');
      $request->session()->forget('paymentId');

      if ($paymentPurpose == 'product purchase') {
        // remove session data
        $request->session()->forget('productCart');
        $request->session()->forget('discount');

        return redirect()->route('shop.purchase_product.cancel');
      } else if ($paymentPurpose == 'equipment booking') {
        // remove session data
        $request->session()->forget('totalPrice');
        $request->session()->forget('equipmentDiscount');

        return redirect()->route('equipment.make_booking.cancel');
      }
    }
  }
}
