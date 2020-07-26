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

Route::get('/', function () {
    return view('home');
})->name('home');

Auth::routes(['verify' => true]);

//User stuff
Route::get('/dashboard', 'DashboardController@index')->name('dashboard')->middleware('verified');

//Admin stuff
Route::get('/info', function () {
    return view('info');
})->middleware('verified','role:admin')->name('info');

//Access denied
Route::get('/not_authorized', function () {
    return view('not_authorized');
})->name('not_authorized');
