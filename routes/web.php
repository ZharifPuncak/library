<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetManagementController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TagController;

/*
|--------------------------------------------------------------------------
| AUTH (GUEST)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    Route::get('admin/register', [RegisterController::class, 'showAdminRegisterForm'])
        ->name('admin.register');
    Route::post('admin/register', [RegisterController::class, 'adminRegister']);
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
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/assets/all', [AssetController::class, 'all'])->name('assets.all');

Route::prefix('assets')->group(function () {
    Route::get('/photos', [AssetController::class, 'photos'])->name('assets.photos');
    Route::get('/videos', [AssetController::class, 'videos'])->name('assets.videos');
    Route::get('/ebooks', [AssetController::class, 'ebooks'])->name('assets.ebooks');
});

Route::get('/asset/{asset}', [AssetController::class, 'show'])->name('assets.show');
Route::get('/collections', [App\Http\Controllers\UserCollectionController::class, 'index'])->name('collections.index');
Route::get('/collections/{name}', [App\Http\Controllers\UserCollectionController::class, 'show'])->name('collections.show');
Route::get('/collections/{name}/asset/{asset}', [App\Http\Controllers\UserCollectionController::class, 'showAsset'])->name('collections.asset.show');

Route::get('/vr', function () {
    try {
        return view('vr.index');
    } catch (\Exception $e) {
        // Debug: If view fails, return error message
        return response('VR View Error: ' . $e->getMessage(), 500);
    }
})->name('vr');

// Alternative direct access route for testing
Route::get('/vr-test', function () {
    return 'VR Route is accessible!';
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'is_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AssetManagementController::class, 'dashboard'])->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | ASSETS MANAGEMENT
        |--------------------------------------------------------------------------
        */
        Route::get('assets', [AssetManagementController::class, 'index'])->name('assets.index');
        Route::get('assets/all', [AssetManagementController::class, 'allAssets'])->name('assets.all');
        Route::get('assets/photos', [AssetManagementController::class, 'photos'])->name('assets.photos');
        Route::get('assets/videos', [AssetManagementController::class, 'videos'])->name('assets.videos');
        Route::get('assets/ebooks', [AssetManagementController::class, 'ebooks'])->name('assets.ebooks');
        Route::get('assets/{id}', [AssetManagementController::class, 'show'])->name('assets.show');

        // CRUD
        Route::get('assets/create', [AssetManagementController::class, 'create'])->name('assets.create');
        Route::post('assets', [AssetManagementController::class, 'store'])->name('assets.store');
        Route::get('assets/{id}/edit', [AssetManagementController::class, 'edit'])->name('assets.edit');
        Route::put('assets/{id}', [AssetManagementController::class, 'update'])->name('assets.update');
        Route::delete('assets/{id}', [AssetManagementController::class, 'destroy'])->name('assets.destroy');

        // Collections (Bulk Upload)
        Route::get('collections', [AssetManagementController::class, 'collections'])->name('collections.index');
        Route::get('collections/create', [AssetManagementController::class, 'bulkCreate'])->name('collections.create');
        Route::post('collections/store', [AssetManagementController::class, 'bulkStore'])->name('collections.store');
        Route::get('collections/{name}', [AssetManagementController::class, 'showCollection'])->name('collections.show');
        Route::get('collections/{name}/asset/{id}', [AssetManagementController::class, 'showCollectionAsset'])->name('collections.asset.show');
      
        /*
        |--------------------------------------------------------------------------
        | CATEGORIES (CLEAN — NO DOUBLE ADMIN PREFIX)
        |--------------------------------------------------------------------------
        */

        // Special management page (if you use standalone page)
        Route::get('categories/management', [CategoryController::class, 'categoryManagement'])
            ->name('categories.categoryManagement');

        // Resource routes → includes:
        // index, create, store, show, edit, update, destroy
        Route::resource('categories', CategoryController::class);
        Route::get('assets/photo', [AssetManagementController::class, 'photo'])
       ->name('assets.photo');




        /*
        |--------------------------------------------------------------------------
        | TAGS
        |--------------------------------------------------------------------------
        */
        // Tag management page
        Route::get('tags/management', [TagController::class, 'manage'])->name('tags.tagManagement');
        
        Route::post('tags', [TagController::class, 'store'])->name('tags.store');
        Route::put('tags/{id}', [TagController::class, 'update'])->name('tags.update');
        Route::delete('tags/{id}', [TagController::class, 'destroy'])->name('tags.destroy');

        /*
        |--------------------------------------------------------------------------
        | SLIDESHOW
        |--------------------------------------------------------------------------
        */
        Route::get('slideshow/manage', [App\Http\Controllers\Admin\SlideshowController::class, 'manage'])->name('slideshow.manage');
        Route::post('slideshow', [App\Http\Controllers\Admin\SlideshowController::class, 'store'])->name('slideshow.store');
        Route::put('slideshow/{id}', [App\Http\Controllers\Admin\SlideshowController::class, 'update'])->name('slideshow.update');
        Route::delete('slideshow/{id}', [App\Http\Controllers\Admin\SlideshowController::class, 'destroy'])->name('slideshow.destroy');
    });
