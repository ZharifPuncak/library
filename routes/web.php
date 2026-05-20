<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MyListController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\TagController;
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
    Route::get('/collections',           [MediaController::class, 'index'])  ->name('media.index');
    Route::get('/collections/create',                       [App\Http\Controllers\UserCollectionController::class, 'create'])   ->name('collections.create');
    Route::post('/collections',                             [App\Http\Controllers\UserCollectionController::class, 'store'])    ->name('collections.store');
    Route::get('/collections/{collection}',                 [App\Http\Controllers\UserCollectionController::class, 'show'])     ->name('collections.show');
    Route::get('/collections/{collection}/edit',            [App\Http\Controllers\UserCollectionController::class, 'edit'])     ->name('collections.edit');
    Route::get('/collections/{collection}/download',        [App\Http\Controllers\UserCollectionController::class, 'download'])  ->name('collections.download');
    Route::put('/collections/{collection}',                 [App\Http\Controllers\UserCollectionController::class, 'update'])   ->name('collections.update');
    Route::delete('/collections/{collection}',              [App\Http\Controllers\UserCollectionController::class, 'destroy'])  ->name('collections.destroy');
    Route::post('/collections/{collection}/media',          [App\Http\Controllers\UserCollectionController::class, 'addMedia']) ->name('collections.media.store');
    Route::put('/collections/{collection}/media/{media}',    [App\Http\Controllers\UserCollectionController::class, 'updateMedia'])->name('collections.media.update');
    Route::delete('/collections/{collection}/media/{media}', [App\Http\Controllers\UserCollectionController::class, 'destroyMedia'])->name('collections.media.destroy');

    Route::get('/vr', [VrController::class, 'index'])->name('vr');
    Route::get('/vr-test', [VrController::class, 'health'])->name('vr.test');

    // Slider management (admin-gated inside the controller)
    Route::get('/slider',             [SliderController::class, 'index'])  ->name('slider.index');
    Route::post('/slider',            [SliderController::class, 'store'])  ->name('slider.store');
    Route::delete('/slider/{slider}', [SliderController::class, 'destroy'])->name('slider.destroy');

    // Categories management (admin-gated inside the controller)
    Route::get('/categories',               [CategoryController::class, 'index'])  ->name('categories.index');
    Route::post('/categories',              [CategoryController::class, 'store'])  ->name('categories.store');
    Route::put('/categories/{category}',    [CategoryController::class, 'update']) ->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Tags management (admin-gated inside the controller)
    Route::get('/tags',                     [TagController::class, 'index'])       ->name('tags.index');
    Route::post('/tags',                    [TagController::class, 'store'])       ->name('tags.store');
    Route::put('/tags/{tag}',               [TagController::class, 'update'])      ->name('tags.update');
    Route::delete('/tags/{tag}',            [TagController::class, 'destroy'])     ->name('tags.destroy');

    // My List (saved collections)
    Route::get('/my-list',                             [MyListController::class, 'index'])             ->name('mylist.index');
    Route::post('/my-list/collections/{collection}',   [MyListController::class, 'storeCollection'])   ->name('mylist.collections.store');
    Route::delete('/my-list/collections/{collection}', [MyListController::class, 'destroyCollection']) ->name('mylist.collections.destroy');

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
