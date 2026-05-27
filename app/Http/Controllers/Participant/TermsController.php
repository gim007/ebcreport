<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Terms of Service / Privacy Policy gate that the legacy site shows before a
 * new participant proceeds to choose an organization. Accepting the terms
 * stores a session flag (`participant.terms_accepted_at`) that downstream
 * controllers can check via `ParticipantTermsAccepted` middleware.
 */
class TermsController extends Controller
{
    public function show(Request $request): View
    {
        return view('participant.terms');
    }

    public function accept(Request $request): RedirectResponse
    {
        $request->validate([
            'accept' => ['required', 'in:yes'],
        ], [
            'accept.required' => 'You must agree to the Privacy Policy and Terms of Service to continue.',
            'accept.in'       => 'You must agree to the Privacy Policy and Terms of Service to continue.',
        ]);

        $request->session()->put('participant.terms_accepted_at', now()->toIso8601String());

        return redirect()->route('participant.organizations');
    }
}
