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

Route::group(['prefix' => 'v1/operator', 'namespace' => 'Operator', 'middleware' => ['auth', 'operator']], function () {

    # Operators
    Route::get('', 'OperatorController@index');
    Route::put('', 'OperatorController@update');

    # Stations
    Route::get('stations', 'StationController@index');
    Route::post('stations', 'StationController@create');
    Route::get('stations/{id}', 'StationController@show');
    Route::put('stations/{id}', 'StationController@update');

    # Captures
    Route::get('captures', 'CaptureController@index');
    Route::post('captures', 'CaptureController@create');
});
