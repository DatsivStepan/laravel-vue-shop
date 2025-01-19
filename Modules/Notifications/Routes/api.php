<?php

use Illuminate\Http\Request;

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

Route::group(['middleware' => 'auth:api', 'as' => 'notifications.', 'prefix' => 'notifications'], function () {
    Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
        Route::group(['prefix' => 'templates', 'as' => 'templates.'], function () {
            Route::get('index', 'NotificationTemplateController@index')->name('index');
            Route::get('/{notificationTemplate}', 'NotificationTemplateController@show')->name('show');
            Route::match(['put', 'patch'], '/{notificationTemplate}',
                'NotificationTemplateController@update')->name('update');
        });

    });
});
