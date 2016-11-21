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

Route::get('/', function () {
    return view('welcome');
});

Route::post('api-routes/black-white-list',['as'=>'black-white-list','uses'=>'ApplicationController@blackWhiteList']);
Route::post('api-routes/app-usage',['as'=>'app-usage','uses'=>'ApplicationController@appUsage']);
Route::post('api-routes/net-usage',['as'=>'net-usage','uses'=>'ApplicationController@netUsage']);
Route::post('api-routes/child-schedule',['as'=>'net-usage','uses'=>'ApplicationController@childSchedule']);
Route::post('api-routes/save-location',['as'=>'save-location','uses'=>'ApplicationController@saveLocation']);
Route::post('api-routes/save-event',['as'=>'save-event','uses'=>'ApplicationController@saveEvent']);
Route::post('api-routes/all-instaled-app',['as'=>'all-instaled-app','uses'=>'ApplicationController@allInstaledApp']);
Route::post('api-routes/change-status-app',['as'=>'change-status-app','uses'=>'ApplicationController@changeStatusApp']);
Route::post('api-routes/change-schedule-app',['as'=>'change-schedule-app','uses'=>'ApplicationController@changeScheduleApp']);
Route::post('api-routes/create-schedule-app',['as'=>'create-schedule-app','uses'=>'ApplicationController@createScheduleApp']);
Route::post('api-routes/all-locations-child',['as'=>'all-locations-child','uses'=>'ApplicationController@allLocationsChild']);
Route::post('api-routes/store-child',['as'=>'store-child','uses'=>'ApplicationController@storeChild']);
Route::post('api-routes/delete-schedule-app',['as'=>'delete-schedule-app','uses'=>'ApplicationController@deleteScheduleApp']);
Route::post('api-routes/create-school-settings',['as'=>'create-school-settings','uses'=>'ApplicationController@createSchoolSettings']);


Route::post('api-routes/store-parents',['as'=>'store-parents','uses'=>'ParentsController@storeParents']);
Route::post('api-routes/login-parents',['as'=>'login-parents','uses'=>'ParentsController@loginParents']);
Route::post('api-routes/deactivation',['as'=>'deactivation','uses'=>'ParentsController@deactivation']);
Route::post('api-routes/activate-frendino-pro',['as'=>'activate-frendino-pro','uses'=>'ParentsController@activateFrendinoPro']);
Route::post('api-routes/get-reset-password',['as'=>'get-reset-password','uses'=>'ParentsController@getResetPassword']);
Route::post('api-routes/change-password',['as'=>'change-password','uses'=>'ParentsController@changePassword']);

