<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('queen', 'QueenController');
Route::get('dates', 'QueenController@index');
Route::post('store', 'QueenController@store');
Route::post('update/{queen}', 'QueenController@update');
Route::post('destroy/{queen}', 'QueenController@destroy');
