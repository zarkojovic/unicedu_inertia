<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Models\Field;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
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


Route::post('/change-lang', function (\Illuminate\Http\Request $request) {
    $lang = $request->lang;
    App()->setLocale($lang);
    Session::put('locale', $lang);
})->name('change-lang');

//Route::get('/', function () {
//    $message = __('messages.welcome');
//    return Inertia::render('Welcome', [
//        'canLogin' => Route::has('login'),
//        'canRegister' => Route::has('register'),
//        'laravelVersion' => Application::VERSION,
//        'phpVersion' => PHP_VERSION,
//        'message' => $message
//    ]);
//})->name("home");

//FOR AUTHENTICATED USERS
Route::middleware('auth')->group(function () {

    //FOR VERIFIED USERS
    Route::middleware('verified')->group(function () {
        Route::get('/', [UserController::class, 'show'])->name("home");

        Route::get('/profile', [UserController::class, 'show'])->name('profile');

        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');


        //EDIT IMAGE
        Route::match(['post', 'put', 'patch'], '/image/edit', [UserController::class, 'updateImage'])->name("user.image.update");
    });

    //LARAVEL STARTER KIT DEFAULT ROUTES {
    Route::get('/profile-edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // }
});

Route::get('/test', function () {
//    return redirect()->back()->with(['toast' => ['message' => 'vracamo sesijuu', 'type' => 'warning']]);


    function filterObjectsByFieldCategoryId($arrayOfObjects, $categoryId)
    {
        $filteredObjects = array_filter($arrayOfObjects, function ($object) use ($categoryId) {
            return isset($object->field_category_id) && $object->field_category_id === $categoryId;
        });

        return array_values($filteredObjects);
    }

    $categories = DB::table('field_categories')
        ->join('field_category_page', 'field_category_page.field_category_id', '=', 'field_category_page.field_category_id')
        ->join('pages', 'field_category_page.page_id', '=', 'pages.page_id')
        ->where('pages.route', '=', '/profile')
        ->where('field_categories.is_visible', 1)
        ->select('field_categories.category_name', 'field_categories.field_category_id')->distinct()->get()->toArray();

    $catId = array();
    foreach ($categories as $object) {
        if (isset($object->field_category_id)) {
            $catId[] = $object->field_category_id;
        }
    };

    $fields = DB::table('fields')
        ->leftJoin('user_infos', function ($join) {
            $join->on('fields.field_id', '=', 'user_infos.field_id')
                ->where('user_infos.user_id', auth()->user()->user_id);
        })
        ->whereIn('fields.field_category_id', $catId)
        ->where('fields.is_active', 1)
        ->select(
            'fields.field_id',
            'fields.title',
            'fields.field_name',
            'fields.is_required',
            'fields.type',
            'fields.order',
            'fields.field_category_id',
            DB::raw('COALESCE(user_infos.value, "") as value'),
            DB::raw('COALESCE(user_infos.display_value, "") as display_value'),
            DB::raw('COALESCE(user_infos.file_name, "") as file_name'),
            DB::raw('COALESCE(user_infos.file_path, "") as file_path')
        )
        ->get()->toArray();

    for ($i = 0; $i < count($fields); $i++) {
        if ($fields[$i]->type == 'enumeration') {
            $FieldItems = \App\Models\FieldItem::where('field_id', $fields[$i]->field_id)->pluck('item_value', 'item_id')->toArray();
            $fields[$i]->items = $FieldItems;
        }
    }

    for ($i = 0; $i < count($categories); $i++) {
        $fieldsWithThisCategories = filterObjectsByFieldCategoryId($fields, $categories[$i]->field_category_id);
        $categories[$i]->fields = $fieldsWithThisCategories;
    }

    dd($categories);


});

//LARAVEL STARTER KIT DEFAULT ROUTES {
//Route::get('/user', [\App\Http\Controllers\UserController::class, 'show'])->name('user.show');

require __DIR__ . '/auth.php';
// }
