<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Rules\Recaptcha;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller {

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required',
            'email' => 'required|string|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'recaptcha' => [new Recaptcha()],
        ]);
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'package_id' => '1',
        ]);

        event(new Registered($user));
        //http://localhost/verify-email/5/7c5f9d36d1b52b8d2ad7769429a25d64711b7e92?expires=1698585561&signature=ec726432c438706e37a8db4b1021d42418d76ef03277223c7cbb7b7778462cbe
        //http://localhost/verify-email/5/7c5f9d36d1b52b8d2ad7769429a25d64711b7e92?expires=1698585635&signature=05662c5bc179480aa0a2b80d5707c3f8c4b0a5ba174fe4034030d9035755409e
        //        Registered::dispatch($user);

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Display the registration view.
     */
    public function create(): Response {
        return Inertia::render('Auth/Register');
    }

}
