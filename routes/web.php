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

Route::get('/admin',function(){

    // $customers = User::whereNotIn('id',Organizer::select(['user_id'])->get(['user_id']))


    $user = User::find(2);
    $adminRole = Role::where('name','=','Admin')->first();

    //  $b = in_array($adminRole,$user->roles );

    $b= false;
    foreach ($user->roles as $role) {
        if($adminRole->id==$role->id)
        $b = true;
        # code...
    }


    dd($b);
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

