<?php

use App\Http\Controllers\Api\PlaceController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\OrganizerController;
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

Route::prefix('/place')->controller(PlaceController::class)->group(function(){

    Route::get('','index');
    Route::get('/popular','popularPlaces');
    Route::middleware('auth:api')->group(function(){
        Route::post('/favorite','favorite');

    });

});

Route::prefix('/user')->controller(UserController::class)->group(function (){
    Route::get('','index');
    Route::post('/login','login');
    Route::post('/register','register');

    Route::post('/promotion/request','requestPromotion');

    Route::post('/confirm','confirmAccount');

    Route::middleware('auth:api')->group(function(){

        Route::post('/profile/customer','profileCustomer');
        Route::post('/profile/customer/edit','editProfileCustomer');
    });
});


Route::prefix('/organizer')->controller(OrganizerController::class)->group(function(){


    Route::middleware('auth:api')->group(function(){
        Route::post('/profile','organizerProfile');
        Route::post('/profile/edit','editOrganizerProfile');
    });

});


