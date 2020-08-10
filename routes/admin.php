<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1/admin', 'namespace' => 'Admin', 'middleware' => ['auth', 'admin']], function () {

    # Operators
    Route::get('operators', 'OperatorController@index');
    Route::post('operators', 'OperatorController@create');
    Route::get('operators/{id}', 'OperatorController@show');
    Route::put('operators/{id}', 'OperatorController@update');

    # Stations
    Route::get('stations', 'StationController@index');
    Route::post('stations', 'StationController@create');
    Route::get('stations/{id}', 'StationController@show');
    Route::put('stations/{id}', 'StationController@update');

    # Captures
    Route::get('captures', 'CaptureController@index');
    Route::post('captures', 'CaptureController@create');
    Route::delete('captures', 'CaptureController@exclude');
});
