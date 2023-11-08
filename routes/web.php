<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\FieldCategoryController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\IntakeController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserAdminController;
use App\Http\Controllers\UserController;
use App\Models\FieldCategory;
use App\Models\Page;
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
        //DYNAMIC ROUTES
        $routeNames = Page::all();
        foreach ($routeNames as $route) {
            if (!empty($route->role->role_name)) {
                switch ($route->role->role_name) {
                    case 'admin':
                        Route::middleware(["admin"])->group(function() use (
                            $route
                        ) {
                            //ADMIN ROUTES
                            Route::get($route->route,
                                function() use ($route) {
                                    return Inertia::render('Dashboard');
                                });
                        });
                        break;
                    case 'student':
                        Route::middleware('package')->group(function() use (
                            $route
                        ) {
                            Route::get($route->route, function() use ($route) {
                                $categoriesWithFields = FieldCategory::getAllCategoriesWithFields($route->route);

                                return Inertia::render('Dashboard', [
                                    'categoriesWithFields' => $categoriesWithFields,
                                ]);
                            });
                        });
                        break;
                    default :
                        Route::get($route->route, function() use ($route) {
                            $categoriesWithFields = FieldCategory::getAllCategoriesWithFields($route->route);

                            return Inertia::render('Dashboard', [
                                'categoriesWithFields' => $categoriesWithFields,
                            ]);
                        });
                        break;
                }
            }
        }

        Route::middleware('package')->group(function() {
            Route::get('/', [UserController::class, 'show'])->name("home");
            Route::get('/profile', [UserController::class, 'show'])
                ->name('profile');
            Route::get('/applications',
                [DealController::class, 'showUserDeals'])
                ->name('applications');
        });
        Route::post('/applications/addNew',
            [DealController::class, 'apply'])
            ->name('newApplication');
        Route::post('/applications/removeDeal',
            [DealController::class, 'deleteDeal'])
            ->name('removeApplication');

        Route::post('/userFieldsUpdate',
            [UserController::class, 'updateUserInfo']);

        //EDIT IMAGE
        Route::match(['post', 'put', 'patch'], '/image/edit',
            [UserController::class, 'updateImage'])
            ->name("user.image.update");

        //ADMIN
        Route::middleware('admin')->prefix('admin')->group(function() {
            //DASHBOARD
            Route::get('/dashboard', [AdminController::class, 'show'])
                ->name('adminDashboard');

            //FIELDS
            Route::get('/fields', [AdminController::class, "home"])
                ->name("admin_home");
            Route::get("/fields-fetch",
                [AdminController::class, "fetchFields"]);
            Route::post("/fields-add",
                [AdminController::class, "setFieldCategory"]);
            Route::post('/update-fields',
                [FieldController::class, 'updateFields'])
                ->name('updateFields');
            Route::post('/fields-modify',
                [FieldController::class, 'setFieldCategory'])
                ->name('setFieldCategory');

            //PAGES ROUTES
            Route::get('/pages',
                [PageController::class, 'showPageListView'])->name('showPage');
            Route::get('/pages/new', [PageController::class, 'createNewPage'])
                ->name('createNewPage');
            Route::post('/pages/insertNew',
                [PageController::class, 'createPage'])
                ->name('addNewPage');
            Route::post('/pages/deletePage',
                [PageController::class, 'deletePage']);
            Route::get('/pages/edit/{id}', [PageController::class, 'editPage'])
                ->name('editPage');
            Route::post('/pages/update', [PageController::class, 'updatePage'])
                ->name('updatePage');

            //CATEGORIES ROUTES
            Route::get('/categories',
                [FieldCategoryController::class, 'showCategory'])
                ->name('showCategory');
            Route::get('/categories/new',
                [FieldCategoryController::class, 'createNewCategory'])
                ->name('createNewCategory');
            Route::post('/categories/insertNew',
                [FieldCategoryController::class, 'insertCategories'])
                ->name('insertCategories');
            Route::post('/categories/deleteCategory', [
                FieldCategoryController::class,
                'deleteCategory',
            ])->name('deleteCategory');

            //APPLICATION ROUTES
            Route::get('/applications',
                [DealController::class, 'showApplication'])
                ->name('showApplication');

            //USER ROUTES
            Route::get('/users',
                [UserAdminController::class, 'showUser'])
                ->name('showUser');
            Route::get('/users/edit/{id}',
                [UserAdminController::class, 'editUser'])
                ->name('editUser');
            Route::post('/users/change-user-package', [
                UserAdminController::class,
                'changeUserPackage',
            ])->name('changeUserPackage');

            //PACKAGE ROUTES
            Route::get('/packages',
                [PackageController::class, 'showPage'])
                ->name('showPackage');
            Route::post('/set-package-pages',
                [PackageController::class, 'setPackagePages'])
                ->name('setPackagePages');

            //INTAKES ROUTES
            Route::get('/intakes',
                [IntakeController::class, 'showIntake'])
                ->name('showIntake');
            Route::post('/intakes/change-active-intake',
                [IntakeController::class, 'changeActiveIntake'])
                ->name('changeActiveIntake');
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
    //    $fieldItems = FieldItem::with('field')->get();

    //    dd($pages);

    dd(Page::getCurrentPagesForSidebar());
    //    FieldCategory::getAllCategoriesWithFields('/profile');
    //
    //    $broze_package_pages = ['/profile', '/applications'];
    //
    //    $page_ids = Page::whereIn('route', $broze_package_pages)
    //        ->pluck('page_id')->toArray();
    //
    //    dd($page_ids);
});

//LARAVEL STARTER KIT DEFAULT ROUTES {
//Route::get('/user', [\App\Http\Controllers\UserController::class, 'show'])->name('user.show');

require __DIR__.'/auth.php';
// }
