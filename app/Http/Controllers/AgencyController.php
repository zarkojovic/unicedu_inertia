<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AgencyController extends Controller {

    public function showAgencies() {
        $data = User::select(
            'profile_image',
            'user_id as id',
            'first_name as Company Name',
            'email as Email',
            'phone as Phone',
            'created_at as Created At',
        )
            ->where('role_id', 2)
            ->paginate(10);
        return Inertia::render('Admin/Agency/Show', [
            'data' => $data,
        ]);
    }

    public function createNewAgency() {
        return Inertia::render('Admin/Agency/Edit');
    }

    public function insertAgency(Request $request) {
        try {
            // Validate request data
            $validatedData = $request->validate([
                'companyName' => 'required|string|max:255',
                'password' => [
                    'required',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/',
                ],
                'confirm_password' => 'required|same:password',
                'phone' => 'required|unique:'.User::class,
                'email' => 'required|string|email|max:255|unique:'.User::class,
                'profileImage' => 'required|image|mimes:jpeg,png,jpg,gif|max:5096',
            ]);

            // Start a database transaction
            DB::beginTransaction();

            // Create a new User instance
            $user = new User();

            $user->first_name = $validatedData['companyName'];
            $user->last_name = '';
            $user->email = $validatedData['email'];
            $user->phone = $validatedData['phone'];
            $user->password = bcrypt($validatedData['password']);
            $user->role_id = 2;

            // Save the user
            $user->save();

            //            dd($request->all());
            // Update user's profile image
            User::updateImage($validatedData['profileImage'], $user->user_id);

            // Commit the database transaction
            DB::commit();
            // Optionally, you can redirect or return a response here

            return redirect()
                ->route('showAgencies')
                ->with('toast',
                    ['type' => 'success', 'message' => 'Agency added']);
        }
        catch (Exception $e) {
            // An error occurred, rollback the database transaction
            DB::rollback();

            // Log the error
            Log::errorLog('Error during agency insertion: '.$e->getMessage());
            // Optionally, you can redirect or return a response indicating the error
        }
    }

}
