<?php

use App\Http\Controllers\WebUI\PromotionController;
use App\Http\Controllers\WebUI\ReportController;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/admin/{id}', function ($id) {

//    FileContentResult


    $photo = \App\Models\PlacePhotos::find($id);


    $img_data = base64_decode($photo->path);
    $image = imagecreatefromstring($img_data);

    $finfo = finfo_open();
    $extension = finfo_buffer($finfo, $img_data, FILEINFO_MIME_TYPE);
    header('Content-Type: image/' . str_replace('image/', '', $extension));
    return imagejpeg($image);


});

Route::get('/', 'WebUI\LoginController@index')->name('home');

Route::get('/login', 'WebUI\LoginController@index')->name('login');
Route::post('/user/login', 'WebUI\LoginController@login')->name('login.perform');
Route::get('/logout', 'WebUI\LogoutController@logout')->name('logout.perform');

/***
 * place
 */
Route::prefix('/place')
    ->middleware(['auth', 'checkAdmin'])
    ->controller('WebUI\PlaceController')
    ->group(function () {
        Route::get('/', 'index')->name('place.index');

        Route::get('/create', 'create')->name('place.create');
        Route::post('/store', 'store')->name('place.store');

        Route::get('/{id}', 'show')->name('place.show');

        Route::get('/{id}/edit', 'edit')->name('place.edit');
        Route::put('/{id}', 'update')->name('place.update');

        Route::get('/{id}/delete', 'delete')->name('place.delete');
        Route::delete('/{id}', 'destroy')->name('place.destroy');
    });


Route::prefix('/place/proposed')->controller('WebUI\ProposedPlacesController')
    ->group(function () {
        Route::get('/index', 'index')->name('place.proposed.index');
        ROute::delete('/{id}', 'delete')->name('place.proposed.destroy');
    });

Route::prefix('/user')
    ->middleware(['auth', 'checkAdmin'])
    ->controller('WebUI\UserController')
    ->group(function () {

        Route::get('/', 'index')->name('user.index');

        Route::get('/blocked', 'blockedUsers')->name('user.blocked.index');

        Route::put('/{id}/unblock', 'unblockUser')->name('user.unblock');

        Route::get('/{id}', 'show')->name('user.show');



    });

Route::prefix('/organizer')
    ->middleware(['auth', 'checkAdmin'])
    ->controller('WebUI\OrgnizerController')
    ->group(function () {
        Route::get('/', 'index')->name('organizer.index');

        Route::get('/{id}', 'show')->name('organizer.show');
    });


Route::prefix('/promotion')
    ->middleware(['auth', 'checkAdmin'])
    ->controller(PromotionController::class)->group(function () {
        Route::get('/index', 'index')->name('promotion.index');
        Route::get('{id}/show', 'show')->name('promotion.show');
        Route::put('/{id}/accept', 'acceptPromotion')->name('promotion.accept');
        Route::put('/{id}/reject', 'rejectPromotion')->name('promotion.reject');
    });

Route::prefix('/report')
    ->controller(ReportController::class)
    ->group(function () {
        Route::get('/index', 'index')->name('report.index');
        Route::get('/{id}', 'show')->name('report.show');

        Route::put('/{id}/accept', 'acceptReport')->name('report.accept');
        Route::put('/{id}/reject', 'rejectReport')->name('report.reject');
    });






//Auth::routes();
//
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

