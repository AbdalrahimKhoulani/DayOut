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


Route::prefix('/place')->controller(PlaceController::class)->group(function () {

    Route::get('', 'index');
    Route::get('/popular/{id}', 'popularPlaces');

    Route::get('/photo/{id}', 'placePhoto');

    Route::middleware('auth:api')->group(function () {
        Route::get('/favorite/{userId}/{placeId}', 'isFavorite');
        Route::post('/favorite', 'favorite');
    });
});

Route::prefix('/user')->controller(UserController::class)->group(function () {
    // Route::get('','index');
    Route::post('/login', 'login');
    Route::post('/register', 'register');

    Route::post('/organizer/register', 'organizerRegister');

    Route::post('/promotion/request', 'requestPromotion');

   // Route::post('/confirm', 'confirmAccount');

    Route::get('/profile/{id}/photo', 'profilePhoto');

    Route::middleware('auth:api')->group(function () {

        Route::get('/profile/customer/{id}', 'profileCustomer');
        Route::post('/profile/customer/edit/{id}', 'editProfileCustomer');
    });
});


Route::prefix('/organizer')->controller(OrganizerController::class)->group(function () {
    Route::get('/profile/{id}', 'organizerProfile');

    Route::middleware('auth:api')->group(function () {

        Route::post('/profile/edit', 'editOrganizerProfile');
    });
});
