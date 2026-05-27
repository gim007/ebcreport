<?php

namespace App\Http\Controllers\Instructor;

use App\Actions\RegisterInstructorAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInstructorRequest;
use App\Models\Organization;
use Illuminate\Auth\Events\Registered;
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

        // R-26 / legacy parity: dispatch the Registered event so Laravel's
        // MustVerifyEmail flow sends an email verification link to the
        // address the instructor just supplied (mirrors instructor_verification.php).
        event(new Registered($instructor->user));

        Auth::loginUsingId($instructor->user->user_id);

        return redirect()->route('instructor.verify.notice')
            ->with('status', 'Account created. Please check your email for a verification link. Your account is also pending admin approval before full access is granted.');
    }
}
