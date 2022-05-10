<?php

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

Route::get('/admin/{id}',function($id){

//    FileContentResult


    $photo = \App\Models\PlacePhotos::find($id);


    $img_data = base64_decode($photo->path);
    $image = imagecreatefromstring($img_data);

    $finfo = finfo_open();
    $extension = finfo_buffer($finfo,$img_data,FILEINFO_MIME_TYPE);
    header('Content-Type: image/'.str_replace('image/','',$extension));
    return imagejpeg($image);


});

Route::get('/', 'WebUI\LoginController@index')->name('home');

Route::get('/login', 'WebUI\LoginController@index')->name('login');
Route::post('/user/login', 'WebUI\LoginController@login')->name('login.perform');
Route::get('/logout','WebUI\LogoutController@logout')->name('logout.perform');

/***
 * place
 */
Route::prefix('/place')->middleware(['auth','checkAdmin'])->controller('WebUI\PlaceController')->group(function (){
    Route::get('/','index')->name('place.index');

    Route::get('/create','create')->name('place.create');
    Route::post('/store','store')->name('place.store');

    Route::get('/{id}','show')->name('place.show');

    Route::get('/{id}/edit','edit')->name('place.edit');
    Route::put('/{id}','update')->name('place.update');

    Route::delete('/{id}','destroy')->name('place.destroy');
});

Route::prefix('/customer')->middleware(['auth','checkAdmin'])->controller('WebUI\CustomerController')->group(function (){
    Route::get('/','index')->name('customer.index');

    Route::get('/{id}','show')->name('customer.show');


});

Route::prefix('/organizer')->middleware(['auth','checkAdmin'])->controller('WebUI\OrgnizerController')->group(function (){
    Route::get('/','index')->name('organizer.index');

    Route::get('/{id}','show')->name('organizer.show');


});








//Auth::routes();
//
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

