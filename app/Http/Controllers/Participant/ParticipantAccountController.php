<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Rules\BillingState;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Self-service account page for participants. Lives at /account. Mirrors the
 * instructor account page in shape:
 *
 *   - GET  /account            → profile + password forms + assessment history
 *   - PUT  /account            → update profile fields
 *   - POST /account/password   → change password (current required)
 *
 * Username and enrollment (instructor / course / credits) are admin-controlled
 * and stay read-only here.
 */
class ParticipantAccountController extends Controller
{
    public function show(): View
    {
        $user        = Auth::user();
        $participant = $user->participant;
        $results     = $participant?->testResults()->latest('result_date')->get();

        return view('participant.account', compact('user', 'participant', 'results'));
    }

    public function update(Request $request): RedirectResponse
    {
        $participant = Auth::user()->participant;
        abort_if(! $participant, 403);

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100', 'regex:/^[\pL\s\'\-\.]+$/u'],
            'last_name'  => ['required', 'string', 'max:100', 'regex:/^[\pL\s\'\-\.]+$/u'],
            'email'      => ['required', 'email:rfc,dns', 'max:255'],
            'gender'     => ['nullable', 'in:Male,Female,Other,Prefer not to say'],
            // R-31: phone optional unless they want SMS recovery.
            'phone'      => ['nullable', 'string', 'max:50', 'regex:/^[\d\s\+\-\(\)\.]+$/'],
            // Mailing address — optional, BillingState enforces US 2-letter when country is US.
            'address'    => ['nullable', 'string', 'max:200'],
            'city'       => ['nullable', 'string', 'max:100'],
            'state'      => ['nullable', 'string', 'max:100', new BillingState($request->input('country'))],
            'zip'        => ['nullable', 'string', 'max:20'],
            'country'    => ['nullable', 'string', 'size:2'],
        ]);

        $participant->fill([
            'stud_fname'   => $data['first_name'],
            'stud_lname'   => $data['last_name'],
            'stud_email'   => $data['email'],
            'stud_gender'  => $data['gender']  ?: null,
            'stud_phone'   => $data['phone']   ?: null,
            'stud_address' => $data['address'] ?: null,
            'stud_city'    => $data['city']    ?: null,
            'stud_state'   => $data['state']   ? strtoupper(trim($data['state'])) : null,
            'stud_zip'     => $data['zip']     ?: null,
            'stud_country' => $data['country'] ? strtoupper(trim($data['country'])) : null,
        ])->save();

        // Keep the User row's email in sync so password reset mail goes to the right place.
        $participant->user?->forceFill(['user_email' => $data['email']])->save();

        return back()->with('status', 'Profile updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
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
