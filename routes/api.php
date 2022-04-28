<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;

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

Route::middleware('auth:api')->get('/user', function () {
    $user = User::where('phone_number', '=', '0937771725')->get()->first();
    return $user;
});

Route::post('/register','API\UserController@register');

Route::post('/organizer/register','API\UserController@organizerRegister');

Route::post('/promotion/request','API\UserController@requestPromotion');

Route::post('/confirm','API\UserController@confirmAccount');
