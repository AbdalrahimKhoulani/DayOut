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

Route::post('/encode', function (Request $request) {
//    $user = User::where('phone_number', '=', '0937771725')->get()->first();

    $img = file_get_contents($request['image']);
    $base64 = base64_encode($img);
    echo $base64;
   // return $user;
});

Route::get('/decode', function () {

});

Route::prefix('/place')->controller(PlaceController::class)->group(function(){

    Route::get('','index');
    Route::get('/popular','popularPlaces');
    Route::middleware('auth:api')->group(function(){
        Route::get('/favorite/{userId}/{placeId}','isFavorite');
        Route::post('/favorite','favorite');

    });

});

Route::prefix('/user')->controller(UserController::class)->group(function (){
   // Route::get('','index');
    Route::post('/login','login');
    Route::post('/register','register');

    Route::post('/organizer/register','organizerRegister');

    Route::post('/promotion/request','requestPromotion');

    Route::post('/confirm','confirmAccount');

    Route::middleware('auth:api')->group(function(){

        Route::get('/profile/customer/{id}','profileCustomer');
        Route::post('/profile/customer/edit','editProfileCustomer');
    });
});


Route::prefix('/organizer')->controller(OrganizerController::class)->group(function(){


    Route::middleware('auth:api')->group(function(){
        Route::get('/profile/{id}','organizerProfile');
        Route::post('/profile/edit','editOrganizerProfile');
    });

});


