<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ParticipantAccountController extends Controller
{
    public function show()
    {
        $user        = Auth::user();
        $participant = $user->participant;
        $results     = $participant?->testResults()->latest('result_date')->get();

        return view('participant.account', compact('user', 'participant', 'results'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->user_password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['user_password' => Hash::make($request->password)]);

        return back()->with('status', 'Password updated.');
    }
}
