<?php

namespace App\Http\Middleware;

use App\Models\Page;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class PackageMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        $routeName = '/'.Route::current()->uri;

        if ($routeName === '//') {
            $routeName = '/profile';
        }

        $page_id = Page::where('route', $routeName)->value('page_id');
        $package_id = auth()->user()->package_id;

        $packagePageCount = DB::table('student_package_pages')
            ->where('package_id', $package_id)
            ->where('page_id', $page_id)
            ->count();

        if ($packagePageCount > 0) {
            return $next($request);
        }

        return redirect()->route('home');
    }

}
