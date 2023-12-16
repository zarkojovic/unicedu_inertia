<?php

namespace App\Http\Controllers;

use App\Models\FieldCategory;
use App\Models\Log;
use App\Models\Page;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PageController extends Controller {

    // We don't use this method anymore
    public function pageCategories(Request $request) {
        $page = $request->input('name');

        $pagesWithRole = DB::table('pages')
            ->join('field_category_page', 'pages.page_id', '=',
                'field_category_page.page_id')
            ->select('field_category_page.field_category_id')
            ->where('pages.route', '=', $page)
            ->get();

        return response()->json($pagesWithRole);
    }

    public function showPageListView(Request $request) {
        try {
            // Fetch all pages from the 'pages' table to be displayed in the list view
            $pages = DB::table('pages')
                ->join('roles', 'roles.role_id', 'pages.role_id')
                ->select('pages.page_id as id', 'pages.title as title',
                    'pages.route as route',
                    'pages.icon as icon',
                    'roles.role_name as role name');
            if ($request->role) {
                $pages = $pages->where('roles.role_id', $request->role);
            }
            $pages = $pages->paginate(10);
          
            return Inertia::render("Admin/Page/Show", [
                // Pages data to be displayed
                'data' => $pages,
                'roles' => fn() => Role::select('role_name as label',
                    DB::raw('CAST(role_id AS CHAR) AS value'))
                    ->get()
                    ->toArray(),
                'role' => fn() => $request->role,
            ]);
        }
        catch (Exception $e) {
            // Log the error
            Log::errorLog("Error showing pages: ".$e->getMessage());

            // Redirect to the home route with an error message
            return redirect()
                ->route('home')
                ->withErrors(['error' => 'An error occurred while fetching pages.']);
        }
    }

    public function editPage(string $id) {
        try {
            // Find the page by its ID
            $page = Page::findOrFail($id);

            //            if (!$page->is_editable) {
            //                throw new Exception('This page is not editable!');
            //            }

            // Fetch all roles and field categories

            // Get selected field category IDs for the page
            $selectedCategories = DB::table('field_category_page')
                ->select('field_category_id')
                ->where('page_id', $id)
                ->get();

            $page->categories = $selectedCategories;
            $roles = Role::pluck('role_name', 'role_id'
            )->toArray();
            $categories = FieldCategory::where('is_visible', '<>', '0')
                ->pluck('category_name', 'field_category_id'
                )
                ->toArray();

            // Return the edit view with necessary data
            return Inertia::render('Admin/Page/Insert', [
                'editPage' => $page,
                // Filtered icon data
                'roles' => $roles,
                // All available roles
                'categories' => $categories,
                // All available field categories
            ]);
        }
        catch (Exception $e) {
            // Log the error
            Log::errorLog("Error editing page: ".$e->getMessage());

            // Redirect to the showPages route with an error message
            return redirect()
                ->route('showPage')
                ->with([
                    'toast' => [
                        'message' => $e->getMessage(),
                        'type' => 'danger',
                    ],
                ]);
        }
    }

    public function createNewPage() {
        // Fetch all roles and field categories

        $roles = Role::pluck('role_name', 'role_id'
        )->toArray();
        $categories = FieldCategory::where('category_name', '<>', 'Hidden')
            ->pluck('category_name', 'field_category_id'
            )
            ->toArray();

        // Return the edit view for inserting a new page with necessary data
        return Inertia::render('Admin/Page/Insert', [
            'roles' => $roles,            // All available roles
            'categories' => $categories,  // All available field categories
        ]);
    }

    //OLD WAY OF USING ICONS - NOW WE USE TABLER ICONS VUE COMPONENT
    //    public function generateIcons() {
    //        // Path to the resource/js directory
    //        $jsPath = resource_path('css/icons/tabler-icons');
    //        //Gets content from json file
    //        $cssContent = file_get_contents($jsPath."/tabler-icons.css");
    //        $pattern = '/\.([a-zA-Z0-9_-]+)/'; // Regular expression to match class names
    //
    //        preg_match_all($pattern, $cssContent, $matches);
    //
    //        $classNames = $matches[1];
    //        $jsonData = json_encode($classNames, JSON_PRETTY_PRINT);
    //
    //        // Path to the public/js directory
    //        $jsPath = resource_path('js');
    //
    //        file_put_contents($jsPath."/tabler.json", $jsonData);
    //    }

    public function updatePage(Request $request) {
        $data = $request->all();

        try {
            // Validate the incoming data based on defined rules
            $validator = Validator::make($request->all(), [
                'title' => [
                    'required',
                    'string',
                    'unique:pages,title,'.$request->id.',page_id',
                ],
                'route' => [
                    'required',
                    'string',
                    'unique:pages,route,'.$request->id.',page_id',
                    'regex:/^\/(?:[a-z0-9_]+\/)*[a-z0-9_]+$/',
                ],
                'icon' => 'required',
                'role_id' => 'required',
            ], [
                'route.regex' => "Route must start with / and can contain characters, numbers, and '_'",
            ]);

            // If validation fails, throw a ValidationException
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            // Find the page to update
            $update = Page::findOrFail($request->id);

            // Update page data
            $update->title = $request->title;
            $update->route = $request->route;
            $update->icon = $request->icon;
            $update->role_id = $request->role_id;

            DB::beginTransaction();

            try {
                // Save the updated page
                $update->save();

                // Delete existing field-category-page relationships for the specified page
                DB::table('field_category_page')
                    ->where('page_id', $request->id)
                    ->delete();

                // Insert new field-category-page relationships if categories are provided
                if (!empty($request->categories)) {
                    foreach ($request->categories as $category) {
                        DB::table('field_category_page')->insert([
                            'field_category_id' => $category,
                            'page_id' => $update->page_id,
                        ]);
                    }
                }

                DB::commit();

                // Log the update and redirect to showPages route
                Log::apiLog("Page ".$update->title." updated in admin panel.",
                    Auth::user()->user_id);

                return redirect()->route('showPage')->with([
                    'toast' => [
                        'message' => "Page ".$update->title." updated successfully!",
                        'type' => 'success',
                    ],
                ]);
            }
            catch (Exception $e) {
                // Rollback the transaction on exception
                DB::rollback();

                // Log the error and redirect with error message
                Log::errorLog("Error updating page: ".$e->getMessage());
                return redirect()
                    ->route('showPage')
                    ->with([
                        'toast' => [
                            'message' => "An error occurred while updating the page.",
                            'type' => 'danger',
                        ],
                    ]);
            }
        }
        catch (ValidationException $e) {
            // Redirect back with validation errors and input data
            Log::errorLog("Error updating page: ".$e->getMessage());
            return redirect()->back()->with([
                'toast' => [
                    'message' => "An error occurred while updating the page.",
                    'type' => 'danger',
                ],
            ])->withInput();
        }
    }

    public function createPage(Request $request) {
        //        dd('poz');
        // Start a database transaction
        DB::beginTransaction();

        try {
            // Validate the incoming data based on defined rules
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|unique:pages',
                'route' => [
                    'required',
                    'unique:pages',
                    'regex:/^\/(?:[a-z0-9_]+\/)*[a-z0-9-]+$/',
                ],
                'icon' => 'required',
                'role_id' => 'required',
            ], [
                'route.regex' => "Route must start with / and can contain characters, numbers, and '-'",
            ]);

            // If validation fails, redirect back with errors and input data
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Create a new Page instance and set its attributes
            $newPage = new Page();
            $newPage->title = $request->title;
            $newPage->route = $request->route;
            $newPage->icon = $request->icon;
            $newPage->role_id = $request->role_id;

            // If page creation fails, throw an exception
            if (!$newPage->save()) {
                session([
                    'toast' => [
                        'message' => 'Something went wrong!',
                        'type' => 'danger',
                    ],
                ]);
                throw new Exception("Page creation failed.");
            }

            // Log the successful creation of the new page
            Log::apiLog("New page - ".$newPage->title." added!");

            // Insert field-category-page relationships if categories are provided
            if ($request->filled('categories')) {
                foreach ($request->categories as $category) {
                    DB::table('field_category_page')->insert([
                        'field_category_id' => $category,
                        'page_id' => $newPage->page_id,
                    ]);
                }
            }

            // Commit the transaction as everything was successful
            DB::commit();
            // Redirect to the showPages route
            return redirect()->route('showPage');
        }
        catch (Exception $e) {
            // Rollback the transaction on exception
            DB::rollback();
            // Log the error and handle it appropriately
            Log::errorLog("Error adding new page");

            // Redirect to the showPages route with an error message
            return redirect()
                ->route('showPage')
                ->with([
                    'toast' => [
                        'message' => $e->getMessage(),
                        'type' => 'danger',
                    ],
                ]);
        }
    }

    public function deletePage(Request $request) {
        try {
            $id = $request->id;

            // Find the page by its ID
            $page = Page::findOrFail($id);

            // If page is found
            if ($page) {
                // Store the title of the page for later use
                $name = $page->title;
                if (!$page->is_editable) {
                    throw new Exception("You can't delete this page!");
                }
                // Begin a database transaction
                DB::beginTransaction();

                try {
                    // Delete the page
                    $page->delete();

                    // Log the successful deletion of the page
                    Log::apiLog("Page '".$page->title."' deleted");

                    // Commit the transaction as everything was successful
                    DB::commit();
                }
                catch (Exception $e) {
                    // Rollback the transaction on exception
                    DB::rollback();

                    // Log the error and handle it appropriately
                    Log::errorLog("Error deleting page: ".$e->getMessage());

                    // Redirect to the showPages route with an error message
                    return redirect()
                        ->route('showPage')
                        ->with([
                            'toast' => [
                                'message' => $e->getMessage(),
                                'type' => 'danger',
                            ],
                        ]);
                }
            }

            // Redirect to the showPages route after successful deletion or if the page was not found
            return redirect()->route('showPage')->with([
                'toast' => [
                    'message' => 'Page was deleted successfully!',
                    'type' => 'success',
                ],
            ]);
        }
        catch (Exception $e) {
            // Log the error and handle it appropriately
            Log::errorLog("Error finding page: ".$e->getMessage());

            // Redirect to the showPages route with an error message
            return redirect()
                ->route('showPage')
                ->with([
                    'toast' => [
                        'message' => 'There was an error during deleting!',
                        'type' => 'danger',
                    ],
                ]);
        }
    }

    //OLD WAY OF DOING IT
    //    public function getIconsByName(Request $request) {
    //        $name = $request->name;
    //
    //        // Path to the resource/js directory
    //        $jsPath = resource_path('js');
    //        //Gets content from json file
    //        $cssContent = file_get_contents($jsPath."/tabler.json");
    //
    //        $icons = json_decode($cssContent);
    //
    //        $icons = array_filter($icons, function($icon) {
    //            return str_contains($icon, 'ti') && $icon != 'ti';
    //        });
    //
    //        $icons = array_filter($icons, function($icon) use ($name) {
    //            return str_contains($icon, strtolower($name));
    //        });
    //
    //        echo(json_encode($icons));
    //    }

}
