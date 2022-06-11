<?php

use App\Http\Controllers\Api\PlaceController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\NotificationsController;
use App\Http\Controllers\Api\OrganizerController;
use App\Models\Place;
use App\Models\User;
use App\Models\UserReport;
use App\Services\FCM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookingsController;
use App\Http\Controllers\Api\CheckOutController;
use Illuminate\Support\Facades\Validator;


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

//TODO: delete profile image for customer, trip rate value for customer


Route::get('place/{id}/show', function ($id) {
    $place = Place::with('photos')->where('id', $id)->first();
    if ($place == null) {
        error_log('Place with id : ' . $id . ' not found');
        return 'Place with id ' . $id . ' not found';
    }
    error_log('Place with id : ' . $id . ' retrieved successfully');
    return $place;
});

Route::middleware('auth:api')->post('user/report', function (Request $request) {

    if (Auth::id() == null)
        error_log('Auth::user()');

    $validator = Validator::make($request->all(), [
        'target_id' => 'required',
        'report' => 'required'
    ]);

    if ($validator->fails()) {
        return $validator->errors();
    }

    $userReport = new UserReport();
    $userReport->reporter_id = Auth::id();
    $userReport->target_id = $request['target_id'];
    $userReport->report = $request['report'];
    $userReport->save();

    error_log('Reported successfully');
    return $userReport;
});


Route::post('/notify', function (Request $request) {

    $user = User::where('id', $request['user_id'])->get();
    $fcm = new FCM();
    $fcm->sendNotification($user, $request['title'], $request['body']);
});


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

    //Route::get('/profile/{id}/photo', 'profilePhoto');
    Route::get('/profile/customer/{id}', 'profileCustomer');


    Route::middleware('auth:api')->group(function () {

        Route::post('/profile/customer/edit', 'editProfileCustomer');
        Route::get('/logout', 'logout');
        Route::put('/mobile-token', 'setMobileToken');
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

        Route::get('/active/{type}', 'getActiveTrips');
        Route::get('/upcoming/{type}', 'getUpcomingTrips');
        Route::get('/history/{type}', 'getHistoryTrips');


        Route::get('/organizer', 'organizerTrip');

        Route::put('/edit', 'editTrip');

        Route::put('/edit/photos', 'editTripPhotos');
        Route::put('/edit/places', 'editTripPlaces');
        Route::put('/edit/types/{id}', 'editTripTypes');

        Route::post('/book', 'bookTrip');
        Route::post('/rate', 'rateTrip');

        Route::put('/{id}/begin', 'beginTrip');
        Route::put('/{id}/end', 'endTrip');


    });
});


Route::prefix('/organizer')->controller(OrganizerController::class)->group(function () {
    Route::get('/profile/{id}', 'organizerProfile');

    Route::middleware('auth:api')->group(function () {

        Route::post('/profile/edit', 'editOrganizerProfile');
        Route::delete('/profile/delete/photo', 'deleteProfileImage');

    });
});


Route::prefix('/search')->controller(SearchController::class)->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('/trip', 'search');
    });
});

Route::prefix('/bookings')->controller(BookingsController::class)->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::get('/trip/{trip_id}', 'getBookingsForTrip');
        Route::get('/trip/{trip_id}/passengers', 'getPassengersForTrip');
        Route::put('/{id}/confirm', 'confirmBooking');
        Route::put('/{id}/cancel', 'cancelBooking');
    });
});

Route::middleware('auth:api')->controller(NotificationsController::class)->group(function () {
    Route::get('/notifications', 'index');
});

Route::middleware('auth:api')->controller(CheckOutController::class)->group(function () {
    Route::post('trip/checkout', 'checkOut');
});

