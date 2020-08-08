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

Route::group(['prefix' => 'v1/public', 'namespace' => 'Open'], function () {

    # Operators
    Route::get('operators', 'OperatorController@index');

    # Stations
    Route::get('stations', 'StationController@index');

    # Captures
    Route::get('captures', 'CaptureController@index');
});
