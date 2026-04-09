<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AppSettingController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check() && auth()->user()->starting_view && !request()->has('home')) {
        return redirect(auth()->user()->starting_view);
    }
    if (auth()->check()) {
        auth()->user()->update(['news_viewed_at' => now()]);
    }
    return view('welcome');
});

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit')->middleware('throttle:5,1');
    Route::get('/login/verify', [AuthController::class, 'showVerify'])->name('login.verify');
    Route::post('/login/verify', [AuthController::class, 'verifyCode'])->name('login.verify.submit')->middleware('throttle:5,1');
    Route::get('/login/set-password', [AuthController::class, 'showSetPassword'])->name('login.set-password');
    Route::post('/login/set-password', [AuthController::class, 'setPassword'])->name('login.set-password.submit')->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

// View routes — permission middleware handles auth + guest access based on nav config
Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('permission:users.view');
Route::get('/roles', [RoleController::class, 'index'])->name('roles.index')->middleware('permission:roles.view');
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index')->middleware('permission:projects.view');
Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show')->middleware('permission:projects.view')->where('project', '[0-9]+');
Route::get('/trash', [TrashController::class, 'index'])->name('trash.index')->middleware('permission:trash.view');
Route::get('/trash/{trash}', [TrashController::class, 'show'])->name('trash.show')->middleware('permission:trash.view');
Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index')->middleware('permission:activity-logs.view');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // User management
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('permission:users.create');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::put('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.update-password');
    Route::patch('/user/theme', [UserController::class, 'updateTheme'])->name('user.theme');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:users.delete');

    // Role management
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:roles.create');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:roles.edit');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:roles.delete');
    Route::post('/roles/{role}/copy', [RoleController::class, 'copy'])->name('roles.copy')->middleware('permission:roles.create');

    // Project management
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create')->middleware('permission:projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store')->middleware('permission:projects.create');
    Route::post('/projects/upload-image', [ProjectController::class, 'uploadImage'])->name('projects.upload-image');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::post('/projects/{project}/copy', [ProjectController::class, 'copy'])->name('projects.copy')->middleware('permission:projects.create');

    // Trash management (write operations)
    Route::delete('/trash/empty', [TrashController::class, 'empty'])->name('trash.empty')->middleware('permission:trash.delete');
    Route::post('/trash/{trash}/restore', [TrashController::class, 'restore'])->name('trash.restore')->middleware('permission:trash.restore');
    Route::delete('/trash/{trash}', [TrashController::class, 'destroy'])->name('trash.destroy')->middleware('permission:trash.delete');

    // Activity log detail
    Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show')->middleware('permission:activity-logs.view');

    // App settings
    Route::get('/settings', [AppSettingController::class, 'index'])->name('settings.index')->middleware('permission:settings.manage');
    Route::put('/settings', [AppSettingController::class, 'update'])->name('settings.update')->middleware('permission:settings.manage');
    Route::get('/settings/nav', [AppSettingController::class, 'navIndex'])->name('settings.nav')->middleware('permission:settings.manage');
    Route::put('/settings/nav', [AppSettingController::class, 'updateNav'])->name('settings.nav.update')->middleware('permission:settings.manage');
});

// Public routes
Route::get('/manual', fn () => view('manual'))->name('manual');
