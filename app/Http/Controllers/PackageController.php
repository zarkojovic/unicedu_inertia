<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Page;
use Illuminate\Http\Request;
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
        foreach ($packages as $package) {
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
    public function update(Request $request, string $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        //
    }

}
