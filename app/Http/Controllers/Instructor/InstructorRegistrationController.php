<?php

namespace App\Http\Controllers\Instructor;

use App\Actions\RegisterInstructorAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInstructorRequest;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;

class InstructorRegistrationController extends Controller
{
    public function show()
    {
        $organizations = Organization::where('is_hidden', false)
            ->orderBy('uni_name')
            ->get(['uni_id', 'uni_name']);

        return view('instructor.register', compact('organizations'));
    }

    public function store(StoreInstructorRequest $request, RegisterInstructorAction $action)
    {
        $instructor = $action->execute($request->validated());

        Auth::loginUsingId($instructor->user->user_id);

        return redirect()->route('instructor.dashboard')
            ->with('status', 'Account created. Your account is pending admin approval before you can access all features.');
    }
}
