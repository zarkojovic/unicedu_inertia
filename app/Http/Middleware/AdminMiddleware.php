<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated and has the "admin" role
        if (auth()->user() && auth()->user()->role_id === 3) {
            return $next($request); // User has the "admin" role, allow access
        }
//        // Redirect or respond with an error message for unauthorized users
        return redirect()->route('home');//->with(['toast' => ['message' => 'You are not allowed to be here!', 'type' => 'danger']]);

    }
}
