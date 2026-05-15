<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MyListController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SlideshowController;
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
    Route::get('/media',                 [MediaController::class, 'index'])  ->name('media.index');
    Route::get('/media/create',          [MediaController::class, 'create']) ->name('media.create');
    Route::post('/media',                [MediaController::class, 'store'])  ->name('media.store');
    Route::get('/media/{media}',         [MediaController::class, 'show'])   ->name('media.show');
    Route::get('/media/{media}/edit',    [MediaController::class, 'edit'])   ->name('media.edit');
    Route::put('/media/{media}',         [MediaController::class, 'update']) ->name('media.update');
    Route::delete('/media/{media}',      [MediaController::class, 'destroy'])->name('media.destroy');
    Route::get('/collections',                              [App\Http\Controllers\UserCollectionController::class, 'index'])    ->name('collections.index');
    Route::get('/collections/create',                       [App\Http\Controllers\UserCollectionController::class, 'create'])   ->name('collections.create');
    Route::post('/collections',                             [App\Http\Controllers\UserCollectionController::class, 'store'])    ->name('collections.store');
    Route::get('/collections/{collection}',                 [App\Http\Controllers\UserCollectionController::class, 'show'])     ->name('collections.show');
    Route::get('/collections/{collection}/edit',            [App\Http\Controllers\UserCollectionController::class, 'edit'])     ->name('collections.edit');
    Route::get('/collections/{collection}/download',        [App\Http\Controllers\UserCollectionController::class, 'download'])  ->name('collections.download');
    Route::put('/collections/{collection}',                 [App\Http\Controllers\UserCollectionController::class, 'update'])   ->name('collections.update');
    Route::delete('/collections/{collection}',              [App\Http\Controllers\UserCollectionController::class, 'destroy'])  ->name('collections.destroy');
    Route::get('/collections/{collection}/media/{media}',   [App\Http\Controllers\UserCollectionController::class, 'showAsset'])->name('collections.media.show');

    Route::get('/vr', [VrController::class, 'index'])->name('vr');
    Route::get('/vr-test', [VrController::class, 'health'])->name('vr.test');

    // Slideshow management (admin-gated inside the controller)
    Route::get('/slideshow',                [SlideshowController::class, 'index'])  ->name('slideshow.index');
    Route::post('/slideshow',               [SlideshowController::class, 'store'])  ->name('slideshow.store');
    Route::delete('/slideshow/{slideshow}', [SlideshowController::class, 'destroy'])->name('slideshow.destroy');

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

    // My List (personal saved media + collections)
    Route::get('/my-list',                                [MyListController::class, 'index'])             ->name('mylist.index');
    Route::get('/my-list/add',                            [MyListController::class, 'add'])               ->name('mylist.add');
    Route::post('/my-list/sync',                          [MyListController::class, 'sync'])              ->name('mylist.sync');
    Route::post('/my-list/media/{media}',                 [MyListController::class, 'store'])             ->name('mylist.store');
    Route::delete('/my-list/media/{media}',               [MyListController::class, 'destroy'])           ->name('mylist.destroy');
    Route::post('/my-list/collections/{collection}',      [MyListController::class, 'storeCollection'])   ->name('mylist.collections.store');
    Route::delete('/my-list/collections/{collection}',    [MyListController::class, 'destroyCollection']) ->name('mylist.collections.destroy');

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
