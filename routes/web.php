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

Auth::routes(['verify' => true]);

// Apply verified middleware to most routes
Route::middleware('verified')->group(function() {

  //User stuff
  Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

  //Admin stuff
  Route::get('/info', function () {
      return view('info');
  })->middleware('role:admin')->name('info');

  Route::resource('users', 'UserController')->only([
    'index', 'create', 'edit', 'destroy'
  ])->middleware('role:admin');

  //Access denied
  Route::get('/not_authorized', function () {
      return view('not_authorized');
  })->name('not_authorized');

});

//Public routes
Route::get('/', function () {
    return view('home');
})->name('home');
