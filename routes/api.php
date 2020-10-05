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

Route::post("topsecret", "LocationController@topSecret");
Route::match(
    [Request::METHOD_GET, Request::METHOD_POST],
    "topsecret_split/{satellite_name}",
    "LocationController@topSecretSplit"
);
