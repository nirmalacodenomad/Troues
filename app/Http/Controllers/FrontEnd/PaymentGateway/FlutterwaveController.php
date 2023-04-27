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
use Illuminate\Support\Facades\Session;

class FlutterwaveController extends Controller
{
  private $public_key, $secret_key;

  public function __construct()
  {
    $data = OnlineGateway::whereKeyword('flutterwave')->first();
    $flutterwaveData = json_decode($data->information, true);

    $this->public_key = $flutterwaveData['public_key'];
    $this->secret_key = $flutterwaveData['secret_key'];
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

    $allowedCurrencies = array('BIF', 'CAD', 'CDF', 'CVE', 'EUR', 'GBP', 'GHS', 'GMD', 'GNF', 'KES', 'LRD', 'MWK', 'MZN', 'NGN', 'RWF', 'SLL', 'STD', 'TZS', 'UGX', 'USD', 'XAF', 'XOF', 'ZMK', 'ZMW', 'ZWD');

    $currencyInfo = $this->getCurrencyInfo();

    // checking whether the base currency is allowed or not
    if (!in_array($currencyInfo->base_currency_text, $allowedCurrencies)) {
      return redirect()->back()->with('error', 'Invalid currency for flutterwave payment.')->withInput();
    }

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
        'paymentMethod' => 'Flutterwave',
        'gatewayType' => 'online',
        'paymentStatus' => 'completed',
        'orderStatus' => 'pending'
      );

      $title = 'Purchase Product';
      $notifyURL = route('shop.purchase_product.flutterwave.notify');

      $customerName = $request['billing_first_name'] . ' ' . $request['billing_last_name'];
      $customerEmail = $request['billing_email'];
      $customerPhone = $request['billing_contact_number'];
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
        'paymentMethod' => 'Flutterwave',
        'gatewayType' => 'online',
        'paymentStatus' => 'completed',
        'shippingStatus' => !$request->filled('shipping_method') ? null : 'pending'
      );

      $title = 'Equipment Booking';
      $notifyURL = route('equipment.make_booking.flutterwave.notify');

      $customerName = $request['name'];
      $customerEmail = $request['email'];
      $customerPhone = $request['contact_number'];
    }


    // send payment to flutterwave for processing
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.flutterwave.com/v3/payments',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => json_encode([
        'tx_ref' => 'FLW | ' . time(),
        'amount' => $calculatedData['grandTotal'],
        'currency' => $currencyInfo->base_currency_text,
        'redirect_url' => $notifyURL,
        'payment_options' => 'card,banktransfer',
        'customer' => [
          'email' => $customerEmail,
          'phone_number' => $customerPhone,
          'name' => $customerName
        ],
        'customizations' => [
          'title' => $title,
          'description' => $title . ' via Flutterwave.'
        ]
      ]),
      CURLOPT_HTTPHEADER => array(
        'authorization: Bearer ' . $this->secret_key,
        'content-type: application/json'
      )
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $responseData = json_decode($response, true);

    //curl end

    // put some data in session before redirect to flutterwave url
    $request->session()->put('paymentFor', $paymentFor);
    $request->session()->put('arrData', $arrData);

    // redirect to payment
    if ($responseData['status'] === 'success') {
      return redirect($responseData['data']['link']);
    } else {
      return redirect()->back()->with('error', 'Error: ' . $responseData['message'])->withInput();
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

    $urlInfo = $request->all();

    if ($urlInfo['status'] == 'successful') {
      $txId = $urlInfo['transaction_id'];

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/{$txId}/verify",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          'authorization: Bearer ' . $this->secret_key,
          'content-type: application/json'
        )
      ));

      $response = curl_exec($curl);

      curl_close($curl);

      $responseData = json_decode($response, true);
      if ($responseData['status'] === 'success') {
        if ($paymentPurpose == 'product purchase') {
          $purchaseProcess = new PurchaseProcessController();

          // store product order information in database
          $orderInfo = $purchaseProcess->storeData($productList, $arrData);

          // then subtract each product quantity from respective product stock
          foreach ($productList as $key => $item) {
            $product = Product::query()->find($key);

            if ($product->product_type == 'physical') {
              $stock = $product->stock - intval($item['quantity']);

              $product->update(['stock' => $stock]);
            }
          }

          //add blance to admin revinue
          $earning = Earning::first();

          $earning->total_revenue = $earning->total_revenue + $orderInfo->grand_total;
          $earning->total_earning = $earning->total_earning + ($orderInfo->grand_total - $orderInfo->tax);
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
    } else {
      $request->session()->forget('paymentFor');
      $request->session()->forget('arrData');

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
