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
    Route::get('operators', 'Admin\OperatorController::index');
    Route::post('operators', 'Admin\OperatorController::create');
    Route::get('operators/{id}', 'Admin\OperatorController::show');
    Route::put('operators/{id}', 'Admin\OperatorController::update');

    # Stations
    Route::get('stations', 'Admin\stationController::index');
    Route::post('stations', 'Admin\stationController::create');
    Route::get('stations/{id}', 'Admin\stationController::show');
    Route::put('stations/{id}', 'Admin\stationController::update');
});
