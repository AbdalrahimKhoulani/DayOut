<?php

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

Route::get('/', 'WebUI\LoginController@index')->name('home');

Route::post('/login', 'WebUI\LoginController@login')->name('login');

/***
 * place
 */
Route::prefix('/place')->controller('WebUI\PlaceController')->group(function (){
    Route::get('/','index')->name('place.index');

    Route::get('/create','create')->name('place.create');
    Route::post('/store','store')->name('place.store');

    Route::get('/{id}','show')->name('place.show');

    Route::get('/{id}/edit','edit')->name('place.edit');
    Route::put('/{id}','update')->name('place.update');

    Route::delete('/{id}','destroy')->name('place.destroy');
});

Route::prefix('/customer')->controller('WebUI\CustomerController')->group(function (){
    Route::get('/','index')->name('customer.index');

    Route::get('/{id}','show')->name('customer.show');


});








//Auth::routes();
//
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

