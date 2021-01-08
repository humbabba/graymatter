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

Auth::routes(['verify' => true,'register' => config('users.new.register')]);

// Apply verified middleware to most routes
Route::middleware(['verified','suspended'])->group(function() {

  //User stuff
  Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

  //Admin stuff
  Route::middleware('role:admin')->group(function() {
    Route::get('/info', function () {
        return view('info');
    })->name('info');

    Route::get('/test', function () {
        return view('test');
    })->name('test');

    Route::get('users/{id}/suspend', 'UserController@suspend')->name('users.suspend');

    Route::resource('users', 'UserController');
  });

  //Access denied
  Route::get('/not_authorized', function () {
      return view('not_authorized');
  })->name('not_authorized');

  Route::get('/suspended', 'SuspendedController@index')->name('suspended')->withoutMiddleware('suspended');

});

//Public routes
Route::get('/', function () {
    return view('home');
})->name('home');
