<?php

namespace App\Http\Controllers;

use App\Models\Intake;
use App\Models\Package;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UserAdminController extends Controller {

    public function changeUserPackage(Request $request) {
        try {
            DB::beginTransaction(); // Start a database transaction

            $user = User::find($request->user_id);

            if ($user) {
                $user->package_id = $request->package_id;

                if ($user->save()) {
                    $activeIntake = Intake::where('active', 1)->first();

                    $userIntake = DB::table('user_intake_packages')
                        ->where('user_id', $user->user_id)
                        ->where('intake_id', $activeIntake->intake_id)
                        ->update([
                            'package_id' => $request->package_id,
                        ]);

                    if ($userIntake) {
                        DB::commit(); // Both operations were successful, commit the transaction
                        return redirect()->back()->with([
                            'toast' => [
                                'message' => 'User package successfully updated!',
                                'type' => 'success',
                            ],
                        ]);
                    }
                    else {
                        DB::rollBack(); // User intake update failed, rollback the transaction
                        throw new Exception('User package update failed.');
                    }
                }
                else {
                    DB::rollBack(); // User package update failed, rollback the transaction
                    throw new Exception('User package update failed.');
                }
            }
            else {
                DB::rollBack(); // User not found, rollback the transaction
                throw new Exception('User not found.');
            }
        }
        catch (Exception $e) {
            DB::rollBack(); // Catch any exceptions and rollback the transaction
            return redirect()->back()->with([
                'toast' => [
                    'message' => $e->getMessage(),
                    'type' => 'danger',
                ],
            ]);
        }
    }

    public function showUser() {
        $users = DB::table('users')
            ->join('roles', 'roles.role_id', 'users.role_id')
            ->select('users.user_id as id', 'users.profile_image',
                'users.first_name',
                'users.last_name',
                'users.email', 'users.phone', 'roles.role_name as role name',
                'users.package_id as package')
            ->paginate(10);

        return Inertia::render(
            "Admin/User/Show",
            [
                'data' => $users,
            ]);
    }

    public function editUser(string $id) {
        $users = User::select('first_name', 'last_name', 'email_verified_at',
            'profile_image', 'contact_id', 'package_id',
            'created_at', 'phone', 'updated_at', "user_id as id")
            ->findOrFail($id);

        $history = DB::table('logs')
            ->join('actions', 'actions.action_id', 'logs.action_id')
            ->select('logs.description', 'logs.created_at',
                'actions.action_name as action')
            ->where('logs.user_id', (int) $id)
            ->paginate(10);

        $packages = Package::select('package_name as label',
            DB::raw('CAST(package_id AS CHAR) AS value'))->get()->toArray();
        return Inertia::render("Admin/User/Edit",
            [
                'userLogs' => $history,
                'userInfo' => $users,
                'packages' => $packages,
            ]);
    }

}
