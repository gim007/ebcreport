<?php

namespace App\Http\Controllers\Participant;

use App\Actions\RegisterParticipantAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreParticipantRequest;
use App\Models\Course;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterParticipantController extends Controller
{
    public function show(int $courseId)
    {
        $course = Course::findOrFail($courseId);

        return view('participant.register', compact('course'));
    }

    public function store(StoreParticipantRequest $request, RegisterParticipantAction $action, int $courseId)
    {
        $course = Course::findOrFail($courseId);

        $scholarship = null;
        if ($code = $request->input('scholarship_code')) {
            $scholarship = Scholarship::where('scholarship_code', $code)->first();
            if (! $scholarship?->isValid()) {
                return back()->withErrors(['scholarship_code' => 'This code is invalid or has expired.'])->withInput();
            }
        }

        $participant = $action->execute($request->validated(), $course, $scholarship);

        Auth::loginUsingId($participant->user->user_id);

        if ($scholarship) {
            return redirect()->route('participant.account')->with('status', 'Registered with scholarship code.');
        }

        return redirect()->route('participant.payment', $courseId);
    }
}
