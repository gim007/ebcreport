<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\UsernameReminderMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

// R-32: separate "Forgot Username" flow (distinct from Forgot Password)
class ForgotUsernameController extends Controller
{
    public function show(): \Illuminate\View\View
    {
        return view('auth.forgot-username');
    }

    public function send(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $users = User::where('user_email', $request->email)
            ->where('user_status', 'Active')
            ->get();

        if ($users->isNotEmpty()) {
            Mail::to($request->email)->send(new UsernameReminderMail($users));
        }

        // Always return the same message to prevent username enumeration
        return back()->with('status', 'If that email is registered, a username reminder has been sent.');
    }
}
