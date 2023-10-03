<?php

use App\Http\Controllers\ProfileController;
use App\Models\Page;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

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

Route::fallback(function () {
    return Inertia::render('404');
});

Route::get('/welcome', function () {
    echo __('messages.welcome');
});

//Route::get('/profile', function () {
//    return Inertia::render('Profile');
//});

Route::post('/change-lang', function (\Illuminate\Http\Request $request) {
    $lang = $request->lang;
    App()->setLocale($lang);
    Session::put('locale', $lang);
})->name('change-lang');

Route::get('/', function () {
    $message = __('messages.welcome');
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'message' => $message
    ]);
})->name("home");

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/test', function () {
    return redirect()->back()->with(['toast' => ['message' => 'vracamo sesijuu', 'type' => 'warning']]);
});

Route::get('/user', [\App\Http\Controllers\UserController::class, 'show'])->name('user.show');


require __DIR__ . '/auth.php';
