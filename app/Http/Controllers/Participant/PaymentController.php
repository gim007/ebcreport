<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Rules\BillingState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class PaymentController extends Controller
{
    /**
     * Legacy parity (student_registration_payment.php): let the participant
     * pick how to pay — card now, or a prepaid/scholarship code — before
     * landing on either checkout page.
     */
    public function choose(int $courseId)
    {
        $course = Course::findOrFail($courseId);

        return view('participant.payment-method', compact('course'));
    }

    public function show(int $courseId)
    {
        $course = Course::findOrFail($courseId);

        return view('participant.payment', compact('course'));
    }

    public function charge(Request $request, int $courseId)
    {
        // R-35: state arrives as the 2-letter abbreviation for US (e.g. IL, TX, CA)
        // and as free-text for international addresses. Validated up front so the
        // value reaches Stripe unchanged — never converted to a numeric id as in
        // the legacy flow.
        $validated = $request->validate([
            'payment_method_id' => ['required', 'string'],
            'billing_country'   => ['nullable', 'string', 'size:2'],          // ISO-3166-1 alpha-2
            'billing_state'     => ['nullable', 'string', new BillingState($request->input('billing_country'))],
            'billing_city'      => ['nullable', 'string', 'max:100'],
            'billing_postal'    => ['nullable', 'string', 'max:20'],
            'billing_line1'     => ['nullable', 'string', 'max:200'],
        ]);

        $course       = Course::findOrFail($courseId);
        $participant  = Auth::user()->participant;
        $stripe       = app(StripeClient::class);

        $amountCents = (int) round($course->course_price * 100);

        $intentParams = [
            'amount'         => $amountCents,
            'currency'       => 'usd',
            'payment_method' => $validated['payment_method_id'],
            'confirm'        => true,
            'return_url'     => route('participant.payment.success', $courseId),
            'metadata'       => [
                'course_id'      => $course->course_id,
                'participant_id' => $participant->stud_id,
            ],
        ];

        // Forward billing address to Stripe if supplied. State is passed verbatim
        // (R-35) so US 2-letter codes stay 2-letter and international free-text
        // is preserved exactly as entered.
        if (! empty($validated['billing_country'])) {
            $intentParams['shipping'] = [
                'name'    => trim(($participant->stud_fname ?? '') . ' ' . ($participant->stud_lname ?? '')),
                'address' => array_filter([
                    'country'     => strtoupper($validated['billing_country']),
                    'state'       => $validated['billing_state']  ?? null,
                    'city'        => $validated['billing_city']   ?? null,
                    'postal_code' => $validated['billing_postal'] ?? null,
                    'line1'       => $validated['billing_line1']  ?? null,
                ]),
            ];
        }

        $intent = $stripe->paymentIntents->create($intentParams);

        if ($intent->status === 'succeeded') {
            $participant->increment('tot_credit');

            return redirect()->route('participant.payment.success', $courseId);
        }

        return back()->withErrors(['payment' => 'Payment could not be completed. Please try again.']);
    }

    public function success(int $courseId)
    {
        $course = Course::findOrFail($courseId);

        return view('participant.payment-success', compact('course'));
    }
}
