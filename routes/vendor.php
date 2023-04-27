<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| vendor Interface Routes
|--------------------------------------------------------------------------
*/

Route::prefix('vendor')->middleware(['guest:vendor', 'change.lang'])->group(function () {
  Route::get('/signup', 'Vendor\VendorController@signup')->name('vendor.signup');
  Route::post('/signup/submit', 'Vendor\VendorController@create')->name('vendor.signup_submit');
  Route::get('/login', 'Vendor\VendorController@login')->name('vendor.login');
  Route::post('/login/submit', 'Vendor\VendorController@authentication')->name('vendor.login_submit');
  Route::get('/forget-password', 'Vendor\VendorController@forget_passord')->name('vendor.forget.password');
  Route::post('/send-forget-mail', 'Vendor\VendorController@forget_mail')->name('vendor.forget.mail');
  Route::get('/reset-password', 'Vendor\VendorController@reset_password')->name('vendor.reset.password');
  Route::post('/update-forget-password', 'Vendor\VendorController@update_password')->name('vendor.update-forget-password');
});

Route::prefix('vendor')->middleware('change.lang')->group(function () {
  Route::get('/dashboard', 'Vendor\VendorController@index')->name('vendor.index');
  Route::get('/email/verify', 'Vendor\VendorController@confirm_email');
});


Route::prefix('vendor')->middleware('auth:vendor', 'Deactive')->group(function () {
  Route::get('dashboard', 'Vendor\VendorController@dashboard')->name('vendor.dashboard');
  Route::get('monthly-income', 'Vendor\VendorController@monthly_income')->name('vendor.monthly_income');
  Route::get('/change-password', 'Vendor\VendorController@change_password')->name('vendor.change_password');
  Route::post('/update-password', 'Vendor\VendorController@updated_password')->name('vendor.update_password');
  Route::get('/edit-profile', 'Vendor\VendorController@edit_profile')->name('vendor.edit.profile');
  Route::post('/profile/update', 'Vendor\VendorController@update_profile')->name('vendor.update_profile');
  Route::get('/logout', 'Vendor\VendorController@logout')->name('vendor.logout');

  // change admin-panel theme (dark/light) route
  Route::post('/change-theme', 'Vendor\VendorController@changeTheme')->name('vendor.change_theme');


  // equipment route start
  Route::prefix('/equipment-management')->group(function () {
    // equipment route
    Route::get('/all-equipment', 'Vendor\EquipmentController@index')->name('vendor.equipment_management.all_equipment');

    Route::get('/create-equipment', 'Vendor\EquipmentController@create')->name('vendor.equipment_management.create_equipment');

    Route::post('/upload-slider-image', 'Vendor\EquipmentController@uploadImage')->name('vendor.equipment_management.upload_slider_image');

    Route::post('/remove-slider-image', 'Vendor\EquipmentController@removeImage')->name('vendor.equipment_management.remove_slider_image');

    Route::post('/store-equipment', 'Vendor\EquipmentController@store')->name('vendor.equipment_management.store_equipment');

    Route::post('/{id}/update-featured', 'Vendor\EquipmentController@updateFeatured')->name('vendor.equipment_management.update_featured');

    Route::get('/edit-equipment/{id}', 'Vendor\EquipmentController@edit')->name('vendor.equipment_management.edit_equipment');

    Route::post('/detach-slider-image', 'Vendor\EquipmentController@detachImage')->name('vendor.equipment_management.detach_slider_image');

    Route::post('/update-equipment/{id}', 'Vendor\EquipmentController@update')->name('vendor.equipment_management.update_equipment');

    Route::post('/delete-equipment/{id}', 'Vendor\EquipmentController@destroy')->name('vendor.equipment_management.delete_equipment');

    Route::post('/bulk-delete-equipment', 'Vendor\EquipmentController@bulkDestroy')->name('vendor.equipment_management.bulk_delete_equipment');
  });
  // equipment route end

  // equipment-booking route start
  Route::prefix('/equipment-booking')->group(function () {
    Route::prefix('/settings')->group(function () {
      // location route
      Route::get('/locations', 'Vendor\LocationController@index')->name('vendor.equipment_booking.settings.locations');

      Route::post('/store-location', 'Vendor\LocationController@store')->name('vendor.equipment_booking.settings.store_location');

      Route::post('/update-location', 'Vendor\LocationController@update')->name('vendor.equipment_booking.settings.update_location');

      Route::post('/delete-location/{id}', 'Vendor\LocationController@destroy')->name('vendor.equipment_booking.settings.delete_location');

      Route::post('/bulk-delete-location', 'Vendor\LocationController@bulkDestroy')->name('vendor.equipment_booking.settings.bulk_delete_location');
    });

    // booking route
    Route::get('/bookings', 'Vendor\BookingController@bookings')->name('vendor.equipment_booking.bookings');

    Route::post('/{id}/update-payment-status', 'Vendor\BookingController@updatePaymentStatus')->name('vendor.equipment_booking.update_payment_status');

    Route::post('/{id}/update-shipping-status', 'Vendor\BookingController@updateShippingStatus')->name('vendor.equipment_booking.update_shipping_status');

    Route::get('/{id}/details', 'Vendor\BookingController@show')->name('vendor.equipment_booking.details');

    Route::post('/{id}/delete', 'Vendor\BookingController@destroy')->name('vendor.equipment_booking.delete');

    Route::post('/bulk-delete', 'Vendor\BookingController@bulkDestroy')->name('vendor.equipment_booking.bulk_delete');

    // report route
    Route::get('/report', 'Vendor\BookingController@report')->name('vendor.equipment_booking.report')->middleware('Deactive');

    Route::get('/export-report', 'Vendor\BookingController@exportReport')->name('vendor.equipment_booking.export_report');
  });
  // equipment-booking route end

  // shipping-method route
  Route::get('/shipping-methods', 'Vendor\VendorController@methodSettings')->name('vendor.equipment_booking.settings.shipping_methods');

  Route::post('/update-method-settings', 'Vendor\VendorController@updateMethodSettings')->name('vendor.equipment_booking.settings.update_method_settings');

  Route::prefix('withdraw')->middleware('Deactive')->group(function () {
    Route::get('/', 'Vendor\VendorWithdrawController@index')->name('vendor.withdraw');
    Route::get('/create', 'Vendor\VendorWithdrawController@create')->name('vendor.withdraw.create');
    Route::get('/get-method/input/{id}', 'Vendor\VendorWithdrawController@get_inputs');

    Route::get('/balance-calculation/{method}/{amount}', 'Vendor\VendorWithdrawController@balance_calculation');

    Route::post('/send-request', 'Vendor\VendorWithdrawController@send_request')->name('vendor.withdraw.send-request');
    Route::post('/witdraw/bulk-delete', 'Vendor\VendorWithdrawController@bulkDelete')->name('vendor.witdraw.bulk_delete_withdraw');
    Route::post('/witdraw/delete', 'Vendor\VendorWithdrawController@Delete')->name('vendor.witdraw.delete_withdraw');
  });

  Route::get('/transcation', 'Vendor\VendorController@transcation')->name('vendor.transcation');
  Route::post('/transcation/delete', 'Vendor\VendorController@destroy')->name('vendor.transcation.delete');
  Route::post('/transcation/bulk-delete', 'Vendor\VendorController@bulk_destroy')->name('vendor.transcation.bulk_delete');

  #====support tickets ============
  Route::get('support/ticket/create', 'Vendor\SupportTicketController@create')->name('vendor.support_ticket.create');
  Route::post('support/ticket/store', 'Vendor\SupportTicketController@store')->name('vendor.support_ticket.store');
  Route::get('support/tickets', 'Vendor\SupportTicketController@index')->name('vendor.support_tickets');
  Route::get('support/message/{id}', 'Vendor\SupportTicketController@message')->name('vendor.support_tickets.message');
  Route::post('support-ticket/zip-upload', 'Vendor\SupportTicketController@zip_file_upload')->name('vendor.support_ticket.zip_file.upload');
  Route::post('support-ticket/reply/{id}', 'Vendor\SupportTicketController@ticketreply')->name('vendor.support_ticket.reply');

  Route::post('support-ticket/delete/{id}', 'Vendor\SupportTicketController@delete')->name('vendor.support_tickets.delete');
  Route::post('support-ticket/bulk/delete/', 'Vendor\SupportTicketController@bulk_delete')->name('vendor.support_tickets.bulk_delete');
});



Route::prefix('vendor')->middleware('change.lang')->group(function () {
  Route::get('/{username}', 'FrontEnd\VendorController@details')->name('frontend.vendor.details');
});
