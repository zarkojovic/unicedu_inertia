<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\FieldCategoryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/

Route::fallback(function() {
    return Inertia::render('404');
});

Route::get('/welcome', function() {
    echo __('messages.welcome');
});

Route::post('/change-lang', function(Request $request) {
    $lang = $request->lang;
    App()->setLocale($lang);
    Session::put('locale', $lang);
})->name('change-lang');

//FOR AUTHENTICATED USERS
Route::middleware('auth')->group(function() {
    //FOR VERIFIED USERS
    Route::middleware('verified')->group(function() {
        Route::get('/', [UserController::class, 'show'])->name("home");

        Route::get('/profile', [UserController::class, 'show'])
            ->name('profile');
        Route::get('/applications', function() {
            return Inertia::render("Student/Applications");
        })->name('applications');

        Route::post('/userFieldsUpdate',
            [UserController::class, 'updateUserInfo']);
        //EDIT IMAGE
        Route::match(['post', 'put', 'patch'], '/image/edit',
            [UserController::class, 'updateImage'])->name("user.image.update");

        Route::middleware('admin')->prefix('admin')->group(function() {
            Route::get('/dashboard',
                [AdminController::class, 'show'])->name('admin_home');
            //PAGES ROUTES
            Route::get('/pages',
                [PageController::class, 'showPageListView'])->name('showPage');
            Route::get('/pages/new', [PageController::class, 'createNewPage'])
                ->name('createNewPage');
            //CATEGORIES ROUTES
            Route::get('/categories',
                [FieldCategoryController::class, 'showCategory'])
                ->name('showCategory');
            Route::get('/categories/new',
                [FieldCategoryController::class, 'createNewCategory'])
                ->name('createNewCategory');
            //APPLICATION ROUTES
            Route::get('/applications',
                [DealController::class, 'showApplication'])
                ->name('showApplication');
            //APPLICATION ROUTES
            Route::get('/users',
                [UserController::class, 'showUser'])
                ->name('showUser');
        });
    });

    //LARAVEL STARTER KIT DEFAULT ROUTES
    Route::get('/profile-edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

Route::get('/test', function() {
    dd(FieldCategory::getAllDealFields());
});

//LARAVEL STARTER KIT DEFAULT ROUTES {
//Route::get('/user', [\App\Http\Controllers\UserController::class, 'show'])->name('user.show');

require __DIR__.'/auth.php';
// }
