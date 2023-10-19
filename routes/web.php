<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Models\Field;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use function App\Models\filterObjectsByFieldCategoryId;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/

Route::fallback(function () {
    return Inertia::render('404');
});

Route::get('/welcome', function () {
    echo __('messages.welcome');
});

Route::post('/change-lang', function (\Illuminate\Http\Request $request) {
    $lang = $request->lang;
    App()->setLocale($lang);
    Session::put('locale', $lang);
})->name('change-lang');


//FOR AUTHENTICATED USERS
Route::middleware('auth')->group(function () {

    //FOR VERIFIED USERS
    Route::middleware('verified')->group(function () {
        Route::get('/', [UserController::class, 'show'])->name("home");

        Route::get('/profile', [UserController::class, 'show'])->name('profile');
        Route::get('/applications',function (){
            return Inertia::render("Student/Applications");
        })->name('applications');

        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');

        Route::post('/userFieldsUpdate', [UserController::class, 'updateUserInfo']);
        //EDIT IMAGE
        Route::match(['post', 'put', 'patch'], '/image/edit', [UserController::class, 'updateImage'])->name("user.image.update");

        Route::middleware('admin')->group(function (){
           Route::get('/admin',function (){
              echo 'alooe';
           });
        });

    });

    //LARAVEL STARTER KIT DEFAULT ROUTES
    Route::get('/profile-edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

Route::get('/test', function() {
    dd(FieldCategory::getAllDealFields());
});

//LARAVEL STARTER KIT DEFAULT ROUTES {
//Route::get('/user', [\App\Http\Controllers\UserController::class, 'show'])->name('user.show');

require __DIR__ . '/auth.php';
// }
