<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Page;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PackageController extends Controller {

    /**
     * Display a listing of the resource.
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     */
    public function showPage() {
        $packages = Package::all();
        $pages = Page::where('role_id', '1')
            ->pluck('title', 'page_id')
            ->toArray();

        // treba mi id page-va koji su u tom paketu

        foreach ($packages as $package) {
            $selectedPackages = DB::table('student_package_pages')
                ->where('package_id', $package->package_id)
                ->pluck('page_id')
                ->toArray();
            $package->active = $selectedPackages;
            $package->pages = $pages;
        }

        return Inertia::render('Admin/Package/Show', [
            'packagePages' => $packages,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function setPackagePages(Request $request) {
        //        dd($request->colors);
        try {
            // Begin a database transaction
            DB::beginTransaction();

            //We are receiving from front array of three colors for package indicator (primary,secondary,text)
            $colors = $request->colors;
            //Pulling the package that needs updating
            $packageForUpdating = Package::find($request->package_id);

            //Setting those colors for that package
            $packageForUpdating->primary_color = $colors[0];
            $packageForUpdating->secondary_color = $colors[1];
            $packageForUpdating->text_color = $colors[2];

            $packageForUpdating->save();
            // Convert request pages to integers
            $pagesArray = array_map('intval', $request->pages);

            // Find page IDs that should be deleted
            $pageIdsThatAreNotThereAnymore = DB::table('student_package_pages')
                ->whereNotIn('page_id', $pagesArray)
                ->where('package_id', $request->package_id)
                ->pluck('page_id')->toArray();

            // Delete pages that are no longer in the list
            DB::table('student_package_pages')
                ->whereIn('page_id', $pageIdsThatAreNotThereAnymore)
                ->where('package_id', $request->package_id)
                ->delete();

            // Insert new pages that should be added
            $pagesThatShouldntBeAddedAgain = DB::table('student_package_pages')
                ->where('package_id', $request->package_id)
                ->pluck('page_id')->toArray();

            $newPages = array_values(array_diff($pagesArray,
                $pagesThatShouldntBeAddedAgain));

            foreach ($newPages as $newPage) {
                DB::table('student_package_pages')->insert([
                    'page_id' => $newPage,
                    'package_id' => $request->package_id,
                ]);
            }

            // Commit the transaction
            DB::commit();

            return redirect()
                ->back()
                ->with([
                    'toast' => [
                        'message' => 'You updated pages!',
                        'type' => 'success',
                    ],
                ]);
        }
        catch (Exception $e) {
            // An error occurred, so roll back the transaction
            DB::rollBack();

            return redirect()
                ->back()
                ->with([
                    'toast' => [
                        'message' => 'Error updating pages: '.$e->getMessage(),
                        'type' => 'danger',
                    ],
                ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        //
    }

}
