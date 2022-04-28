<?php

use App\Http\Controllers\Api\UserController;
use App\Models\User;
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

Route::middleware('auth:api')->get('/user', function () {
    $user = User::where('phone_number', '=', '0937771725')->get()->first();
    return $user;
});

Route::prefix('/user')->controller(UserController::class)->group(function (){
    Route::get('','index');
    Route::post('/login','login');
    Route::post('/register','register');

    Route::post('/promotion/request','requestPromotion');

    Route::post('/confirm','confirmAccount');
});


