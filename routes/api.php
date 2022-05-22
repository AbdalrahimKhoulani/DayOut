<?php

use App\Http\Controllers\Api\PlaceController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\NotificationsController;
use App\Http\Controllers\Api\OrganizerController;
use App\Models\User;
use App\Services\FCM;
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


Route::get('/notify', function (Request $request) {

    $user = User::find($request['user_id']);

    $fcm = new FCM();

    $fcm->sendNotification($user, $request['title'], $request['body']);
});

//


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
    Route::get('/profile/customer/{id}', 'profileCustomer');


    Route::middleware('auth:api')->group(function () {

        Route::post('/profile/customer/edit', 'editProfileCustomer');
    });
});

Route::prefix('/trip')->controller(TripController::class)->group(function () {

    Route::get('', 'getTrips');
    Route::get('/{id}/details', 'getTripDetails');
    Route::get('/types', 'getTypes');

    Route::get('/photo/{id}/base64', 'tripPhotoAsBase64');

    Route::get('/{trip_id}/photos', 'getTripPhotos');


    Route::middleware('auth:api')->group(function () {

        Route::post('/create', 'createTrip');
        Route::post('/create/add/photos', 'addTripPhotos');
        Route::post('/create/add/places', 'addPlacesToTrip');
        Route::post('/create/add/types', 'addTripType');

        Route::get('/active', 'getActiveTrips');
        Route::get('/upcoming', 'getUpcomingTrips');
        Route::get('/history', 'getHistoryTrips');


        Route::get('/organizer', 'organizerTrip');

        Route::put('/{id}/edit', 'editTrip');

        Route::put('/edit/photos', 'editTripPhotos');
        Route::put('/edit/places', 'editTripPlaces');
        Route::put('/{id}/edit/types', 'editTripTypes');


        Route::post('/book', 'bookTrip');
        Route::post('/rate', 'rateTrip');
    });
});


Route::prefix('/organizer')->controller(OrganizerController::class)->group(function () {
    Route::get('/profile/{id}', 'organizerProfile');

    Route::middleware('auth:api')->group(function () {

        Route::post('/profile/edit', 'editOrganizerProfile');

    });
});


Route::prefix('/search')->controller(SearchController::class)->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('/trip', 'search');
    });
});

Route::middleware('auth:api')->controller(NotificationsController::class)->group(function () {
    Route::get('/notifications', 'index');
});

