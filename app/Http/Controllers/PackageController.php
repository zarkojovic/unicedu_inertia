<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Package;
use App\Models\Page;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PackageController extends Controller {

    public function showPage() {
        // Fetch all packages from the 'packages' table
        $packages = Package::all();
        // Fetch all pages from the 'pages' table where 'role_id' is 1 (student)
        $pages = Page::where('role_id', '1')
            ->pluck('title', 'page_id')
            ->toArray();

        // Loop through the packages and add the pages to each package
        foreach ($packages as $package) {
            $selectedPackages = DB::table('student_package_pages')
                ->where('package_id', $package->package_id)
                ->pluck('page_id')
                ->toArray();
            $package->active = $selectedPackages;
            $package->pages = $pages;
        }

        // Return the admin template view with necessary data
        return Inertia::render('Admin/Package/Show', [
            'packagePages' => $packages,
        ]);
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
            Log::errorLog('Error updating pages: '.$e->getMessage());
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

}
