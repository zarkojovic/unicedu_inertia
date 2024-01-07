<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AgentController extends Controller {

    //
    public function AgentDashboard() {
        return Inertia::render('Agent/Dashboard',
            ["img" => asset("storage/profile/thumbnail/".auth()->user()->profile_image)]);
    }

    public function AgentStudents() {
        $agentStudents = User::getAgentStudents();
        return Inertia::render('Agent/Students',
            ['agentStudents' => $agentStudents]);
    }

    public function agentStudentProfile($id) {
        $student = User::where('user_id', $id)->first();
        return Inertia::render('Agent/StudentProfile',
            ['student' => $student]);
    }

    public function addNewStudentToAgent(Request $request) {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|unique:'.User::class,
            'email' => 'required|string|email|max:255|unique:'.User::class,
        ]);

        $agent = auth()->user();
        $newStudent = new User();
        $newStudent->first_name = $request->first_name;
        $newStudent->last_name = $request->last_name;
        $newStudent->email = $request->email;
        $newStudent->phone = $request->phone;
        $newStudent->password = bcrypt(Str::random(8));
        $newStudent->role_id = 1;
        $newStudent->agent_id = $agent->user_id;
        $newStudent->save();

        return redirect()
            ->back()
            ->with('toast',
                ['type' => 'success', 'message' => 'Student added to agent']);
    }

}
