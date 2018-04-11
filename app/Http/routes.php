<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
// ADMINISTRATOR
Route::group([
    'middleware' => ['sanitizeRequestData', 'csrf', 'cors'],
], function () {
    Route::auth();

    Route::group(['middleware' => ['auth:web']], function () {
        Route::group(['middleware' => ['role:ADMINISTRATOR|TECHNICAL_SUPPORT_ENGINEER|CLINICAL_SUPPORT_ENGINEER|STORE_ADMINISTRATOR']], function () {
            Route::get('/', 'HomeController@index');
        });

        Route::group(['middleware' => ['role:ADMINISTRATOR']], function () {
            Route::get('/users', 'UserController@index');
            Route::get('/user', 'UserController@create');
            Route::post('/user', 'UserController@create');
            Route::get('/user/{id}', 'UserController@update');
            Route::put('/user/{id}', 'UserController@update');
            Route::put('/user/{id}/active', 'UserController@active');
            Route::get('/user/{id}/details', 'UserController@details');
            Route::get('/user/{id}/password', 'UserController@password');
            Route::put('/user/{id}/password', 'UserController@password');
            Route::get('/user/{id}/serviceRequests', 'ServiceRequestController@userIndex');
            Route::get('/user/{id}/serviceLog', 'ServiceLogController@userIndex');
        });

        Route::group(['middleware' => ['role:ADMINISTRATOR']], function () {
            Route::get('/customers', 'CustomerController@index');
            Route::get('/customer', 'CustomerController@create');
            Route::post('/customer', 'CustomerController@create');
            Route::get('/customer/{id}', 'CustomerController@update');
            Route::put('/customer/{id}', 'CustomerController@update');
            Route::delete('/customer/{id}/logo', 'CustomerController@deleteLogo');
            Route::delete('/customer/{id}', 'CustomerController@delete');
            Route::get('/customer/{id}/details', 'CustomerController@details');
            Route::get('/customer/{id}/devices', 'DeviceController@index');
            Route::get('/customer/{id}/serviceRequests', 'ServiceRequestController@customerIndex');
            Route::put('/customer/{id}/active', 'CustomerController@active');
            Route::get('/customer/{id}/password', 'CustomerController@password');
            Route::put('/customer/{id}/password', 'CustomerController@password');
        });

        Route::group(['middleware' => ['role:ADMINISTRATOR']], function () {
            Route::get('/customer/{id}/persons', 'CustomerContactPersonController@index');
            Route::get('/customer/{id}/person', 'CustomerContactPersonController@create');
            Route::post('/customer/{id}/person', 'CustomerContactPersonController@create');
            Route::get('/customer/{id}/person/{personId}', 'CustomerContactPersonController@update');
            Route::put('/customer/{id}/person/{personId}', 'CustomerContactPersonController@update');
            Route::delete('/customer/{id}/person/{personId}', 'CustomerContactPersonController@delete');
        });

        Route::group(['middleware' => ['role:ADMINISTRATOR']], function () {
            Route::get('/suppliers', 'SupplierController@index');
            Route::get('/supplier', 'SupplierController@create');
            Route::post('/supplier', 'SupplierController@create');
            Route::get('/supplier/{id}', 'SupplierController@update');
            Route::put('/supplier/{id}', 'SupplierController@update');
            Route::delete('/supplier/{id}/logo', 'SupplierController@deleteLogo');
            Route::delete('/supplier/{id}', 'SupplierController@delete');
            Route::get('/supplier/{id}/details', 'SupplierController@details');
        });

        Route::group(['middleware' => ['role:ADMINISTRATOR']], function () {
            Route::get('/supplier/{id}/persons', 'SupplierContactPersonController@index');
            Route::get('/supplier/{id}/person', 'SupplierContactPersonController@create');
            Route::post('/supplier/{id}/person', 'SupplierContactPersonController@create');
            Route::get('/supplier/{id}/person/{personId}', 'SupplierContactPersonController@update');
            Route::put('/supplier/{id}/person/{personId}', 'SupplierContactPersonController@update');
            Route::delete('/supplier/{id}/person/{personId}', 'SupplierContactPersonController@delete');
        });

        Route::group(['middleware' => ['role:ADMINISTRATOR']], function () {
            Route::get('/supplier/{id}/deviceModels', 'DeviceModelController@index');
            Route::get('/supplier/{id}/deviceModel', 'DeviceModelController@create');
            Route::post('/supplier/{id}/deviceModel', 'DeviceModelController@create');
            Route::get('/supplier/{id}/deviceModel/{modelId}', 'DeviceModelController@update');
            Route::put('/supplier/{id}/deviceModel/{modelId}', 'DeviceModelController@update');
            Route::delete('/supplier/{id}/deviceModel/{modelId}/photo', 'DeviceModelController@deletePhoto');
        });

        Route::group(['middleware' => ['role:ADMINISTRATOR|TECHNICAL_SUPPORT_ENGINEER|CLINICAL_SUPPORT_ENGINEER']], function () {
            Route::get('/devices', 'DeviceController@index');
            Route::get('/device', 'DeviceController@create');
            Route::post('/device', 'DeviceController@create');
            Route::get('/device/{id}', 'DeviceController@update');
            Route::put('/device/{id}', 'DeviceController@update');
            Route::get('/device/{id}/details', 'DeviceController@details');
            Route::get('/device/{id}/warranty', 'DeviceController@warranty');
            Route::put('/device/{id}/warranty', 'DeviceController@warranty');
            Route::get('/device/{id}/serviceRequests', 'ServiceRequestController@deviceIndex');
            Route::get('/device/{id}/serviceLog', 'DeviceController@serviceLog');
            Route::get('/device/{id}/serviceReport', 'DeviceController@serviceReport');
        });

        Route::group(['middleware' => ['role:ADMINISTRATOR']], function () {
            Route::get('/devicesModels', 'DeviceModelController@index');
            Route::get('/devicesModel', 'DeviceModelController@create');
            Route::post('/devicesModel', 'DeviceModelController@create');
            Route::get('/devicesModel/{id}', 'DeviceModelController@update');
            Route::put('/devicesModel/{id}', 'DeviceModelController@update');
            Route::delete('/devicesModel/{id}/photo', 'DeviceModelController@deletePhoto');
            Route::delete('/devicesModel/{id}', 'DeviceModelController@delete');
        });

        Route::group(['middleware' => ['role:ADMINISTRATOR']], function () {
            Route::get('/serviceRequests', 'ServiceRequestController@index');
            Route::get('/serviceRequest/{id}/details', 'ServiceRequestController@details');
            Route::put('/serviceRequest/{id}/close', 'ServiceRequestController@close');
            Route::delete('/serviceRequest/{id}', 'ServiceRequestController@delete');
        });

        Route::group(['middleware' => ['role:ADMINISTRATOR|TECHNICAL_SUPPORT_ENGINEER|CLINICAL_SUPPORT_ENGINEER']], function () {
            Route::get('/serviceLog', 'ServiceLogController@index');
        });

        Route::group(['middleware' => ['role:ADMINISTRATOR|STORE_ADMINISTRATOR']], function () {
            Route::get('/salesRequests', 'SalesRequestController@index');
            Route::get('/salesRequests/{id}', 'SalesRequestController@show');
            Route::get('/salesRequest/{id}/status/{status}', 'SalesRequestController@update');
            Route::put('/salesRequest/{id}/status/{status}', 'SalesRequestController@update');
            //Route::delete('/salesRequest/{id}/status/{status}', 'SalesRequestController@delete');
            Route::get('/salesRequest/{id}/notification', 'SalesRequestController@notification');
            Route::post('/salesRequest/{id}/notification', 'SalesRequestController@notification');
        });

        Route::group(['middleware' => ['role:ADMINISTRATOR']], function () {
            Route::get('/complains', 'ComplainController@index');
            Route::get('/complain/{id}', 'ComplainController@update');
            Route::put('/complain/{id}', 'ComplainController@update');
            Route::delete('/complain/{id}', 'ComplainController@delete');
            Route::get('/complain/{id}/notification', 'ComplainController@notification');
            Route::post('/complain/{id}/notification', 'ComplainController@notification');
        });
    });
});

// api
Route::group(['prefix' => 'api/v1/', 'middleware' => 'cors'], function () {
    Route::post('customers/auth/login', 'Api\Customer\Auth\AuthController@login');
    Route::post('engineers/auth/login', 'Api\Engineer\Auth\AuthController@login');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::put('customers/password/reset', 'Api\Customer\Auth\PasswordController@reset');
        Route::put('engineers/password/reset', 'Api\Engineer\Auth\PasswordController@reset');

        Route::group(['prefix' => 'customers/self/'], function () {
            Route::get('devices', 'Api\Customer\DeviceController@index');
            Route::get('devices/{id}/serviceRequests/create', 'Api\Customer\ServiceRequestController@create');
            Route::post('devices/{id}/serviceRequests', 'Api\Customer\ServiceRequestController@store');
            Route::get('devicesModels', 'Api\Customer\DeviceController@modelsIndex');

            Route::get('serviceRequests/{id}', 'Api\Customer\ServiceRequestController@show');
            Route::put('serviceRequests/{id}/support', 'Api\Customer\ServiceRequestController@support');
            Route::put('serviceRequests/{id}/close', 'Api\Customer\ServiceRequestController@close');
            Route::post('serviceRequests/{id}/ratings', 'Api\Customer\ServiceRequestController@rating');
            Route::post('serviceRequests/images/create', 'Api\Customer\ServiceRequestController@storeImage');
            Route::post('serviceRequests/images/base64/create', 'Api\Customer\ServiceRequestController@storeImageBase64');

            Route::post('salesRequests', 'Api\Customer\SalesRequestController@store');

            Route::post('complainRequests', 'Api\Customer\ComplainRequestController@store');

            Route::get('notifications', 'Api\Customer\NotificationsController@index');
            Route::get('notifications/{id}', 'Api\Customer\NotificationsController@show');
        });

        Route::get('serviceRequests', 'Api\Engineer\ServiceRequestController@index');
        Route::get('serviceRequests/{id}', 'Api\Engineer\ServiceRequestController@show');
        Route::put('serviceRequests/{id}/attend', 'Api\Engineer\ServiceRequestController@attend');
        Route::put('serviceRequests/{id}/complete', 'Api\Engineer\ServiceRequestController@complete');
        Route::put('serviceRequests/{id}/pending', 'Api\Engineer\ServiceRequestController@pending');
        Route::put('serviceRequests/{id}/reschedule', 'Api\Engineer\ServiceRequestController@reschedule');

        Route::get('customers/{id}', 'Api\Engineer\CustomerController@show');
        Route::put('customers/{id}/location', 'Api\Engineer\CustomerController@location');
        Route::get('customers/{id}/devices', 'Api\Engineer\DeviceController@index');

        Route::get('devices', 'Api\Engineer\DeviceController@index');
        Route::post('devices/search', 'Api\Engineer\DeviceController@search');
        Route::get('devices/{id}', 'Api\Engineer\DeviceController@show');
        Route::get('devices/{id}/serviceLog', 'Api\Engineer\ServiceLogController@index');
        Route::get('devices/{id}/serviceLog/create', 'Api\Engineer\ServiceLogController@create');
        Route::post('devices/{id}/serviceLog', 'Api\Engineer\ServiceLogController@store');

        Route::get('preventiveMaintenance/tasks', 'Api\Engineer\PreventiveMaintenanceController@index');
        Route::put('preventiveMaintenance/tasks/{id}/complete', 'Api\Engineer\PreventiveMaintenanceController@complete');
        Route::put('preventiveMaintenance/tasks/{id}/uncomplete', 'Api\Engineer\PreventiveMaintenanceController@uncomplete');

        Route::post('tokens', 'Api\AppTokensController@store');
        Route::delete('tokens', 'Api\AppTokensController@delete');
    });
});