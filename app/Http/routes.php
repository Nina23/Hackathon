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

