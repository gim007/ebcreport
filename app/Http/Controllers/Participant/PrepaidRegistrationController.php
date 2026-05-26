<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrepaidRegistrationController extends Controller
{
    public function show()
    {
        return view('participant.prepaid');
    }

    public function redeem(Request $request)
    {
        $request->validate(['code' => ['required', 'string', 'max:100']]);

        $scholarship = Scholarship::where('scholarship_code', $request->code)->first();

        if (! $scholarship?->isValid()) {
            return back()->withErrors(['code' => 'This code is invalid, expired, or fully used.']);
        }

        $participant = Auth::user()?->participant;
        if (! $participant) {
            return back()->withErrors(['code' => 'No participant account linked to this user.']);
        }

        $scholarship->redeem();
        $participant->increment('tot_credit');

        return redirect()->route('participant.account')
            ->with('status', 'Credit added successfully.');
    }
}
