<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        //        if (Session::get('locale') != null) {
        //            App::setLocale(Session::get('locale'));
        //        } else {
        //            App::setLocale('en');
        //            Session::put('locale', 'en');
        //        }

        return $next($request);
    }

}
