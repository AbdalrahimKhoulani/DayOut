<?php

use App\Http\Controllers\Api\FavoritesController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\MailController;
use App\Http\Controllers\Api\PlaceController;
use App\Http\Controllers\Api\PollController;
use App\Http\Controllers\Api\RoadMapController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SmsController;
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


Route::post('/notify', function (Request $request) {

    $user = User::where('id', $request['user_id'])->get();

    $fcm = new FCM();
    $fcm->sendNotification($user, $request['title'], $request['body']);
});


Route::prefix('/place')->controller(PlaceController::class)->group(function () {

    Route::get('', 'index');
    Route::get('/popular/{id}', 'popularPlaces');
    Route::get('/details/{place_id}', 'placeDetails');

    Route::get('/photo/{id}', 'placePhoto');

    Route::middleware('auth:api')->group(function () {
        Route::get('/favorite/{userId}/{placeId}', 'isFavorite');
        Route::post('/favorite', 'favorite');
        Route::post('/suggest','suggestPlace');
    });
});

Route::prefix('/user')->controller(UserController::class)->group(function () {
    // Route::get('','index');
    Route::post('/login', 'login');
    Route::post('/register', 'register');

    Route::post('/organizer/register', 'organizerRegister');

    Route::post('/password/request','requestResetPassword');

    Route::post('/password/reset','resetPassword');

    Route::post('/promotion/request', 'requestPromotion');

    // Route::post('/confirm', 'confirmAccount');

    //Route::get('/profile/{id}/photo', 'profilePhoto');
    Route::get('/profile/customer/{id}', 'profileCustomer');


    Route::middleware('auth:api')->group(function () {

        Route::put('/profile/customer/edit', 'editProfileCustomer');
        Route::delete('/profile/delete/photo', 'deleteProfileImage');
        Route::get('/logout', 'logout');
        Route::put('/mobile-token', 'setMobileToken');
        Route::post('/report', 'reportUser');
    });
});

Route::prefix('/trip')->controller(TripController::class)->group(function () {

    Route::get('', 'getTrips');
    Route::get('/types', 'getTypes');
    Route::get('/{id}/details','getTripDetails');
    Route::get('/{id}/details/organizer','getTripDetailsOrganizer');


    Route::get('/photo/{id}/base64', 'tripPhotoAsBase64');

    Route::get('/{trip_id}/photos', 'getTripPhotos');


    Route::middleware('auth:api')->group(function () {

        Route::post('/create', 'createTrip');

        Route::post('/create/add/photos', 'addTripPhotos');
        Route::post('/create/add/places', 'addPlacesToTrip');
        Route::post('/create/add/types', 'addTripType');






        Route::post('/active/organizer','getActiveTrips4Organizer');
        Route::post('/active/customer','getActiveTrips4Customer');

        Route::post('/upcoming/organizer','getUpcomingTrips4Organizer');
        Route::post('/upcoming/customer','getUpcomingTrips4Customer');

        Route::post('/history/organizer','getHistoryTrips4Organizer');
        Route::post('/history/customer','getHistoryTrips4Customer');

//        Route::get('/active/{type}', 'getActiveTrips');

//        Route::get('/upcoming/{type}', 'getUpcomingTrips');
//        Route::get('/history/{type}', 'getHistoryTrips');


        Route::get('/organizer', 'organizerTrip');

        Route::put('/edit', 'editTrip');
        Route::delete('/{trip_id}/delete','deleteTrip');
        Route::put('/edit/photos', 'editTripPhotosMultiPart');
        Route::put('/edit/places', 'editTripPlaces');
        Route::put('/edit/types/{id}', 'editTripTypes');

        Route::post('/book', 'bookTrip');
        Route::post('/rate', 'rateTrip');

        Route::put('/{id}/begin', 'beginTrip');
        Route::put('/{id}/end', 'endTrip');

        Route::put('/place-status/update/{trip_id}/{place_id}', 'updatePlaceStatus');

    });
});

Route::prefix('/trip/road-map')->middleware('auth:api')
    ->controller(RoadMapController::class)->group(function () {

        Route::get('/{trip_id}', 'getRoadMapPlaces');


    });

Route::prefix('/organizer')->controller(OrganizerController::class)->group(function () {
    Route::get('/profile/{id}', 'organizerProfile');

    Route::middleware('auth:api')->group(function () {
        Route::get('/index', 'index');

        Route::put('/profile/edit', 'editOrganizerProfile');
        Route::delete('/profile/delete/photo', 'deleteProfileImage');

    });
});


Route::prefix('/search')->controller(SearchController::class)->group(function () {
    Route::post('/place','searchForPlace');
    Route::post('/trip', 'searchForTrip');


});

Route::prefix('/bookings')->controller(BookingsController::class)->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::get('/trip/{trip_id}', 'getBookingsForTrip');
        Route::get('/trip/{trip_id}/passengers', 'getPassengersForTrip');

       // Route::put('/{id}/confirm', 'confirmBooking');
       // Route::put('/{id}/cancel', 'cancelBooking');
        Route::put('/customer/{customer_id}/trip/{trip_id}/confirm', 'confirmBooking');
        Route::put('/customer/{customer_id}/trip/{trip_id}/cancel', 'cancelConfirmBooking');

        Route::put('/{trip_id}/user/cancel', 'cancelBookingByUser');
        Route::put('/{id}/organizer/cancel', 'cancelBookingByOrganizer');
        Route::post('/book', 'bookTrip');

    });
});

Route::middleware('auth:api')->controller(NotificationsController::class)->group(function () {
    Route::get('/notifications', 'index');
});

Route::middleware('auth:api')->controller(CheckOutController::class)->group(function(){
    Route::post('trip/checkout','checkOut');
});

Route::prefix('/polls')->controller(PollController::class)->group(function () {

    Route::get('', 'index');

    Route::middleware('auth:api')->group(function () {
        Route::get('/organizer', 'organizerPolls');
        Route::post('/create', 'create');
        Route::put('/vote/{poll_id}/{poll_choice_id}', 'vote');
        Route::delete('/delete/{poll_id}','delete');
    });
});

Route::prefix('/favorites')->middleware('auth:api')->controller(FavoritesController::class)->group(function (){

    Route::get('/places','getFavoritePlaces');

});

Route::prefix('/followers')->middleware('auth:api')->controller(FollowController::class)->group(function(){
    Route::put('/follow/{organizer_user_id}','followOrganizer');
    Route::get('','getFollowedOrganizers');
});


