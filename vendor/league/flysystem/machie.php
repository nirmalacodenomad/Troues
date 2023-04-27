<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Interface Routes
|--------------------------------------------------------------------------
*/

Route::get('product-invoice', function () {
  return view('frontend.equipment.invoice');
});


Route::post('/push-notification/store-endpoint', 'FrontEnd\PushNotificationController@store');

Route::get('/change-language', 'FrontEnd\MiscellaneousController@changeLanguage')->name('change_language');

Route::post('/store-subscriber', 'FrontEnd\MiscellaneousController@storeSubscriber')->name('store_subscriber');

Route::get('/offline', 'FrontEnd\HomeController@offline');

Route::middleware('change.lang')->group(function () {
  Route::get('/', 'FrontEnd\HomeController@index')->name('index');

  Route::prefix('/equipment')->group(function () {
    Route::get('/', 'FrontEnd\Instrument\EquipmentController@index')->name('all_equipment');

    Route::get('/{slug}', 'FrontEnd\Instrument\EquipmentController@show')->name('equipment_details');

    Route::get('/{id}/min-price', 'FrontEnd\Instrument\EquipmentController@minPrice');

    Route::post('/change-shipping-method', 'FrontEnd\Instrument\EquipmentController@changeShippingMethod');

    Route::post('/apply-coupon', 'FrontEnd\Instrument\EquipmentController@applyCoupon');

    Route::prefix('/make-booking')->group(function () {
      Route::post('', 'FrontEnd\Instrument\BookingProcessController@index')->name('equipment.make_booking');

      Route::get('/paypal/notify', 'FrontEnd\PaymentGateway\PayPalController@notify')->name('equipment.make_booking.paypal.notify');

      Route::get('/instamojo/notify', 'FrontEnd\PaymentGateway\InstamojoController@notify')->name('equipment.make_booking.instamojo.notify');

      Route::get('/paystack/notify', 'FrontEnd\PaymentGateway\PaystackController@notify')->name('equipment.make_booking.paystack.notify');

      Route::get('/flutterwave/notify', 'FrontEnd\PaymentGateway\FlutterwaveController@notify')->name('equipment.make_booking.flutterwave.notify');

      Route::post('/razorpay/notify', 'FrontEnd\PaymentGateway\RazorpayController@notify')->name('equipment.make_booking.razorpay.notify');

      Route::get('/mercadopago/notify', 'FrontEnd\PaymentGateway\MercadoPagoController@notify')->name('equipment.make_booking.mercadopago.notify');

      Route::get('/mollie/notify', 'FrontEnd\PaymentGateway\MollieController@notify')->name('equipment.make_booking.mollie.notify');

      Route::post('/paytm/notify', 'FrontEnd\PaymentGateway\PaytmController@notify')->name('equipment.make_booking.paytm.notify');

      Route::get('/complete/{type?}', 'FrontEnd\Instrument\BookingProcessController@complete')->name('equipment.make_booking.complete')->middleware('change.lang');

      Route::get('/cancel', 'FrontEnd\Instrument\BookingProcessController@cancel')->name('equipment.make_booking.cancel');
    });

    Route::post('/{id}/store-review', 'FrontEnd\Instrument\EquipmentController@storeReview')->name('equipment_details.store_review');
  });

  Route::prefix('/shop')->group(function () {
    Route::get('/products', 'FrontEnd\Shop\ProductController@index')->name('shop.products');

    Route::prefix('/product')->group(function () {
      Route::get('/{slug}', 'FrontEnd\Shop\ProductController@show')->name('shop.product_details');

      Route::get('/{id}/add-to-cart/{quantity}', 'FrontEnd\Shop\ProductController@addToCart')->name('shop.product.add_to_cart');
    });

    Route::get('/cart', 'FrontEnd\Shop\ProductController@cart')->name('shop.cart');

    Route::post('/update-cart', 'FrontEnd\Shop\ProductController@updateCart')->name('shop.update_cart');

    Route::get('/cart/remove-product/{id}', 'FrontEnd\Shop\ProductController@removeProduct')->name('shop.cart.remove_product');

    Route::prefix('/checkout')->group(function () {
      Route::get('', 'FrontEnd\Shop\ProductController@checkout')->name('shop.checkout');

      Route::post('/apply-coupon', 'FrontEnd\Shop\ProductController@applyCoupon');

      Route::get('/offline-gateway/{id}/check-attachment', 'FrontEnd\Shop\ProductController@checkAttachment');
    });

    Route::prefix('/purchase-product')->group(function () {
      Route::post('', 'FrontEnd\Shop\PurchaseProcessController@index')->name('shop.purchase_product');

      Route::get('/paypal/notify', 'FrontEnd\PaymentGateway\PayPalController@notify')->name('shop.purchase_product.paypal.notify');

      Route::get('/instamojo/notify', 'FrontEnd\PaymentGateway\InstamojoController@notify')->name('shop.purchase_product.instamojo.notify');

      Route::get('/paystack/notify', 'FrontEnd\PaymentGateway\PaystackController@notify')->name('shop.purchase_product.paystack.notify');

      Route::get('/flutterwave/notify', 'FrontEnd\PaymentGateway\FlutterwaveController@notify')->name('shop.purchase_product.flutterwave.notify');

      Route::post('/razorpay/notify', 'FrontEnd\PaymentGateway\RazorpayController@notify')->name('shop.purchase_product.razorpay.notify');

      Route::get('/mercadopago/notify', 'FrontEnd\PaymentGateway\MercadoPagoController@notify')->name('shop.purchase_product.mercadopago.notify');

      Route::get('/mollie/notify', 'FrontEnd\PaymentGateway\MollieController@notify')->name('shop.purchase_product.mollie.notify');

      Route::post('/paytm/notify', 'FrontEnd\PaymentGateway\PaytmController@notify')->name('shop.purchase_product.paytm.notify');

      Route::get('/complete/{type?}', 'FrontEnd\Shop\PurchaseProcessController@complete')->name('shop.purchase_product.complete')->middleware('change.lang');

      Route::get('/cancel', 'FrontEnd\Shop\PurchaseProcessController@cancel')->name('shop.purchase_product.cancel');
    });

    Route::post('/product/{id}/store-review', 'FrontEnd\Shop\ProductController@storeReview')->name('shop.product_details.store_review');
  });

  Route::get('/vendors', 'FrontEnd\VendorController@index')->name('frontend.vendors');

  Route::prefix('vendor')->group(function () {
    Route::post('review', 'FrontEnd\VendorController@review')->name('vendor.review');
    Route::post('contact/message', 'FrontEnd\VendorController@contact')->name('vendor.contact.message');
  });


  Route::prefix('/blog')->group(function () {
    Route::get('', 'FrontEnd\BlogController@index')->name('blog');

    Route::get('/{slug}', 'FrontEnd\BlogController@show')->name('blog_details');
  });

  Route::get('/faq', 'FrontEnd\FaqController@faq')->name('faq');

  Route::prefix('/contact')->group(function () {
    Route::get('', 'FrontEnd\ContactController@contact')->name('contact');

    Route::post('/send-mail', 'FrontEnd\ContactController@sendMail')->name('contact.send_mail')->withoutMiddleware('change.lang');
  });
});

Route::post('/advertisement/{id}/count-view', 'FrontEnd\MiscellaneousController@countAdView');

Route::prefix('/user')->middleware(['guest:web', 'change.lang'])->group(function () {
  Route::prefix('/login')->group(function () {
    // user redirect to login page route
    Route::get('', 'FrontEnd\UserController@login')->name('user.login');

    // user login via facebook route
    Route::prefix('/facebook')->group(function () {
      Route::get('', 'FrontEnd\UserController@redirectToFacebook')->name('user.login.facebook');

      Route::get('/callback', 'FrontEnd\UserController@handleFacebookCallback');
    });

    // user login via google route
    Route::prefix('/google')->group(function () {
      Route::get('', 'FrontEnd\UserController@redirectToGoogle')->name('user.login.google');

      Route::get('/callback', 'FrontEnd\UserController@handleGoogleCallback');
    });
  });

  // user login submit route
  Route::post('/login-submit', 'FrontEnd\UserController@loginSubmit')->name('user.login_submit')->withoutMiddleware('change.lang');

  // user forget password route
  Route::get('/forget-password', 'FrontEnd\UserController@forgetPassword')->name('user.forget_password');

  // send mail to user for forget password route
  Route::post('/send-forget-password-mail', 'FrontEnd\UserController@forgetPasswordMail')->name('user.send_forget_password_mail')->withoutMiddleware('change.lang');

  // reset password route
  Route::get('/reset-password', 'FrontEnd\UserController@resetPassword');

  // user reset password submit route
  Route::post('/reset-password-submit', 'FrontEnd\UserController@resetPasswordSubmit')->name('user.reset_password_submit')->withoutMiddleware('change.lang');

  // user redirect to signup page route
  Route::get('/signup', 'FrontEnd\UserController@signup')->name('user.signup');

  // user signup submit route
  Route::post('/signup-submit', 'FrontEnd\UserController@signupSubmit')->name('user.signup_submit')->withoutMiddleware('change.lang');

  // signup verify route
  Route::get('/signup-verify/{token}', 'FrontEnd\UserController@signupVerify')->withoutMiddleware('change.lang');
});

Route::prefix('/user')->middleware(['auth:web', 'account.status', 'change.lang'])->group(function () {
  // user redirect to dashboard route
  Route::get('/dashboard', 'FrontEnd\UserController@redirectToDashboard')->name('user.dashboard');

  // edit profile route
  Route::get('/edit-profile', 'FrontEnd\UserController@editProfile')->name('user.edit_profile');

  // update profile route
  Route::post('/update-profile', 'FrontEnd\UserController@updateProfile')->name('user.update_profile')->withoutMiddleware('change.lang');

  // change password route
  Route::get('/change-password', 'FrontEnd\UserController@changePassword')->name('user.change_password');

  // update password route
  Route::post('/update-password', 'FrontEnd\UserController@updatePassword')->name('user.update_password')->withoutMiddleware('change.lang');

  // equipment bookings route
  Route::get('/equipment-bookings', 'FrontEnd\UserController@bookings')->name('user.equipment_bookings');

  // booking details route
  Route::get('/equipment-booking/{id}/details', 'FrontEnd\UserController@bookingDetails')->name('user.equipment_booking.details');

  // product orders route
  Route::get('/product-orders', 'FrontEnd\UserController@orders')->name('user.product_orders');

  Route::prefix('/product-order')->group(function () {
    // order details route
    Route::get('/{id}/details', 'FrontEnd\UserController@orderDetails')->name('user.product_order.details');

    // download digital product route
    Route::post('/product/{id}/download', 'FrontEnd\UserController@downloadProduct')->name('user.product_order.product.download');
  });

  // user logout attempt route
  Route::get('/logout', 'FrontEnd\UserController@logoutSubmit')->name('user.logout')->withoutMiddleware('change.lang');
});

// service unavailable route
Route::get('/service-unavailable', 'FrontEnd\MiscellaneousController@serviceUnavailable')->name('service_unavailable')->middleware('exists.down');

/*
|--------------------------------------------------------------------------
| admin frontend route
|--------------------------------------------------------------------------
*/

Route::prefix('/admin')->middleware('guest:admin')->group(function () {
  // admin redirect to login page route
  Route::get('/', 'BackEnd\AdminController@login')->name('admin.login');

  // admin login attempt route
  Route::post('/auth', 'BackEnd\AdminController@authentication')->name('admin.auth');

  // admin forget password route
  Route::get('/forget-password', 'BackEnd\AdminController@forgetPassword')->name('admin.forget_password');

  // send mail to admin for forget password route
  Route::post('/mail-for-forget-password', 'BackEnd\AdminController@forgetPasswordMail')->name('admin.mail_for_forget_password');
});


/*
|--------------------------------------------------------------------------
| Custom Page Route For UI
|--------------------------------------------------------------------------
*/
Route::get('/{slug}', 'FrontEnd\PageController@page')->name('dynamic_page')->middleware('change.lang');

// fallback route
Route::fallback(function () {
  return view('errors.404');
})->middleware('change.lang');
