<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateUserDealPackage;
use App\Models\Deal;
use App\Models\Intake;
use App\Models\Log;
use App\Models\Package;
use App\Models\Role;
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

            if (!$user) {
                throw new Exception('User not found.');
            }

            // Update user package
            $user->package_id = $request->package_id;

            if (!$user->save()) {
                throw new Exception('User package update failed.');
            }

            // Update user intake package
            $activeIntake = Intake::where('active', 1)->first();

            if (!$activeIntake) {
                throw new Exception('Active intake not found.');
            }

            // Check if the row exists before updating
            $userIntakeExists = DB::table('user_intake_packages')
                ->where('user_id', $user->user_id)
                ->where('intake_id', $activeIntake->intake_id)
                ->exists();

            if ($userIntakeExists) {
                $userIntake = DB::table('user_intake_packages')
                    ->where('user_id', $user->user_id)
                    ->where('intake_id', $activeIntake->intake_id)
                    ->update(['package_id' => $request->package_id]);

                if (!$userIntake) {
                    throw new Exception('User intake update failed.');
                }
            }

            $userIntakePackageId = DB::table('user_intake_packages')
                ->where('user_id', $user->user_id)
                ->where('intake_id', $activeIntake->intake_id)
                ->pluck('user_intake_package_id')
                ->first();

            // Fetch active deals
            $activeDeals = Deal::where('user_intake_package_id',
                $userIntakePackageId)
                ->where('user_id', $user->user_id)
                ->where('active', 1)
                ->get()->pluck('bitrix_deal_id')->toArray();

            $bitrixPackageId = Package::where('package_id',
                $request->package_id)->pluck('package_bitrix_id')->first();
            //            dd($activeDeals, $bitrixPackageId);
            if (count($activeDeals) > 0) {
                // Uncomment the following lines when ready to dispatch the job
                UpdateUserDealPackage::dispatch($activeDeals, $bitrixPackageId);
            }

            DB::commit(); // Both operations were successful, commit the transaction

            return redirect()->back()->with([
                'toast' => [
                    'message' => 'User package successfully updated!',
                    'type' => 'success',
                ],
            ]);
        }
        catch (Exception $e) {
            DB::rollBack(); // Catch any exceptions and rollback the transaction
            Log::errorLog('Error updating user package: '.$e->getMessage());
            return redirect()->back()->with([
                'toast' => [
                    'message' => $e->getMessage(),
                    'type' => 'danger',
                ],
            ]);
        }
    }

    public function changeUserRole(Request $request) {
        try {
            $userId = $request->user_id;
            $newRoleId = $request->role_id;

            // Check if the user is trying to change their own role
            if (auth()->id() == $userId && auth()->user()->role_id != $newRoleId) {
                throw new Exception("You cannot change your own role.");
            }

            $userToUpdate = User::findOrFail($userId);
            $userToUpdate->role_id = $newRoleId;

            if ($userToUpdate->save()) {
                return redirect()->back()->with([
                    'toast' => [
                        'type' => 'success',
                        'message' => 'Successfully changed user role!',
                    ],
                ]);
            }
            else {
                throw new Exception('Failed to save changes.');
            }
        }
        catch (Exception $e) {
            Log::errorLog('Error changing user role: '.$e->getMessage());
            return redirect()->back()->with([
                'toast' => [
                    'type' => 'danger',
                    'message' => 'Error: '.$e->getMessage(),
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
            'profile_image', 'contact_id', 'package_id', 'role_id',
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

        $roles = Role::select('role_name as label',
            DB::raw('CAST(role_id AS CHAR) AS value'))->get()->toArray();

        return Inertia::render("Admin/User/Edit",
            [
                'userLogs' => $history,
                'userInfo' => $users,
                'packages' => $packages,
                'roles' => $roles,
            ]);
    }

}
