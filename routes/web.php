<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VrController;

/*
|--------------------------------------------------------------------------
| AUTH (GUEST)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

// Logout
Route::post('logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| USER / PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
// Public index — accessible to guests.
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', fn () => redirect()->route('home'));

// Everything else requires login.
Route::middleware('auth')->group(function () {
    Route::get('/media',          [MediaController::class, 'index']) ->name('media.index');
    Route::get('/media/create',   [MediaController::class, 'create'])->name('media.create');
    Route::post('/media',         [MediaController::class, 'store']) ->name('media.store');
    Route::get('/media/{media}',  [MediaController::class, 'show'])  ->name('media.show');
    Route::get('/collections', [App\Http\Controllers\UserCollectionController::class, 'index'])->name('collections.index');
    Route::get('/collections/{name}', [App\Http\Controllers\UserCollectionController::class, 'show'])->name('collections.show');
    Route::get('/collections/{name}/media/{media}', [App\Http\Controllers\UserCollectionController::class, 'showAsset'])->name('collections.media.show');

    Route::get('/vr', [VrController::class, 'index'])->name('vr');
    Route::get('/vr-test', [VrController::class, 'health'])->name('vr.test');

    // Profile (own password change)
    Route::get('/profile',           [ProfileController::class, 'edit'])          ->name('profile.edit');
    Route::put('/profile/password',  [ProfileController::class, 'updatePassword'])->name('profile.password');

    // User management (admin-gated inside the controller)
    Route::get('/users',              [UserController::class, 'index'])  ->name('users.index');
    Route::get('/users/create',       [UserController::class, 'create']) ->name('users.create');
    Route::post('/users',             [UserController::class, 'store'])  ->name('users.store');
    Route::get('/users/{user}/edit',  [UserController::class, 'edit'])   ->name('users.edit');
    Route::put('/users/{user}',       [UserController::class, 'update']) ->name('users.update');
    Route::delete('/users/{user}',    [UserController::class, 'destroy'])->name('users.destroy');
});
