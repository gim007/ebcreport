<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Rules\BillingState;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Self-service profile + password page for instructors. Lives at
 * /instructor/account. Mirrors the participant account page in shape:
 *
 *   - GET  /instructor/account            → profile form + password form
 *   - POST /instructor/account            → update profile fields
 *   - POST /instructor/account/password   → change password (current required)
 *
 * Editable fields cover everything except the organization (which is
 * structural and only an admin should reassign), the username (immutable),
 * and approval status.
 */
class InstructorAccountController extends Controller
{
    public function show(): View
    {
        $instructor = Auth::user()->instructor;
        abort_if(! $instructor, 403);

        return view('instructor.account', compact('instructor'));
    }

    public function update(Request $request): RedirectResponse
    {
        $instructor = Auth::user()->instructor;
        abort_if(! $instructor, 403);

        $data = $request->validate([
            'title'        => ['nullable', 'string', 'max:50'],
            'first_name'   => ['required', 'string', 'max:100', 'regex:/^[\pL\s\'\-\.]+$/u'],
            'last_name'    => ['required', 'string', 'max:100', 'regex:/^[\pL\s\'\-\.]+$/u'],
            'gender'       => ['nullable', 'in:Male,Female,Other,Prefer not to say'],
            'email'        => ['required', 'email:rfc,dns', 'max:255'],
            'phone'        => ['required', 'string', 'max:50', 'regex:/^[\d\s\+\-\(\)\.]+$/'],
            'address'      => ['required', 'string', 'max:200'],
            'address_cont' => ['nullable', 'string', 'max:200'],
            'city'         => ['required', 'string', 'max:100'],
            'state'        => ['required', 'string', 'max:100', new BillingState($request->input('country'))],
            'zip'          => ['required', 'string', 'max:20'],
            'country'      => ['required', 'string', 'size:2'],
            'timezone'     => ['required', 'string', 'max:50'],
        ]);

        $instructor->fill([
            'ins_title'        => $data['title']        ?: null,
            'ins_fname'        => $data['first_name'],
            'ins_lname'        => $data['last_name'],
            'ins_gender'       => $data['gender']       ?: null,
            'ins_email'        => $data['email'],
            'ins_phone'        => $data['phone'],
            'ins_address'      => $data['address'],
            'ins_address_cont' => $data['address_cont'] ?: null,
            'ins_city'         => $data['city'],
            'ins_state'        => strtoupper(trim($data['state'])),
            'ins_zip'          => $data['zip'],
            'ins_country'      => strtoupper(trim($data['country'])),
            'ins_timezone'     => $data['timezone'],
        ])->save();

        // Keep the User row's email in sync so password reset / verification mail go to the right place.
        $instructor->user?->forceFill(['user_email' => $data['email']])->save();

        return back()->with('status', 'Profile updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        if (! Hash::check($request->input('current_password'), $user->user_password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['user_password' => Hash::make($request->input('password'))]);

        return back()->with('status', 'Password updated.');
    }
}
