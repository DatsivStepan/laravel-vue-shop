<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api', 'as' => 'api.payments.'], function () {
    Route::group(['middleware' => 'permission:managePayments'], function () {
        Route::get('admin/payments/get-options/{pagetype}', 'PaymentsController@getOptions');
        Route::get('admin/payments/get-field-settings/{type}', 'PaymentsController@getFieldSettings');
        Route::post('admin/payments/actions/{id}', 'PaymentsController@actions')->name('actions');
        Route::apiResource('admin/payments', 'PaymentsController');
    });
    // Route::any('payments-method/paypal/return', 'PayPalController@return')->name('paypal.return');

});

Route::group(['prefix' => 'payments/paypal', 'as' => 'api.payments.paypal.'], function () {
    Route::any('/webhook/{tenant_id}', 'PaypalController@webhook')->name('webhook');

    Route::any('capture/{tenant_id}', 'PaypalController@captureOrder')->name('capture');
    Route::any('activate/{tenant_id}', 'PaypalController@activateSubscription')->name('activateSubscription');
});

Route::group(['prefix' => 'payments/stripe', 'as' => 'api.payments.stripe.'], function () {
    Route::any('/webhook/{tenant_id}', 'StripeController@webhook')->name('webhook');
});


