<?php

namespace App\Http\Middleware;

use App\Models\FieldCategory;
use App\Models\Intake;
use App\Models\Package;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware {

    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'img' => auth()->check() ? asset("storage/profile/tiny/".$request->user()->profile_image) : '',
            ],
            'ziggy' => fn() => [
                //                ...(new Ziggy())->toArray(), //this returns all routes to front, which I don't see a reason for (Martin)
                'location' => $request->url(),
            ],
            'toast' => session('toast'),
            'sidebar_pages' => auth()->check() ? Page::getCurrentPagesForSidebar() : [],
            'current_route_uri' => Route::current()->uri,
            'documents_root' => asset("storage/profile/documents/").'/',
            'images_root' => asset("storage/profile/original/").'/',
            'deal_fields' => Route::current()->uri === 'applications' ? FieldCategory::getAllDealFields() : NULL,
            'recaptcha_site_key' => config('services.recaptcha.site_key'),
            'active_intake' => Intake::where('active', '1')->first(),
            'packages' => Package::select('package_name', 'package_id',
                'primary_color',
                'secondary_color', 'text_color')->get()->toArray(),
        ];
    }

}
