<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Privacy Policy &amp; Terms of Service &mdash; {{ config('app.name') }}</title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f9fafb; color: #1f2937; line-height: 1.55; }
        a { color: #1565c0; }
        .bar-strip { display: flex; height: 6pt; }
        .bar-strip .b { flex: 1; }
        .bar-strip .b.d { background: #2e7d32; }
        .bar-strip .b.i { background: #c62828; }
        .bar-strip .b.s { background: #1565c0; }
        .bar-strip .b.c { background: #f9a825; }
        .bar-edge { height: 4px; background: #111827; }
        .wrap { max-width: 860px; margin: 0 auto; padding: 40px 24px 60px; }
        h1 { font-size: 28px; color: #111827; margin: 0 0 6px; letter-spacing: -0.3px; }
        .sub { color: #6b7280; margin-bottom: 28px; }
        .terms { background: #fff; border-radius: 12px; padding: 28px 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); max-height: 460px; overflow-y: auto; font-size: 14px; }
        .terms h2 { font-size: 16px; margin: 16px 0 6px; color: #111827; }
        .terms p { margin: 0 0 10px; color: #374151; }
        .form-card { background: #fff; border-radius: 12px; padding: 20px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); margin-top: 16px; }
        .err { background: #fee2e2; color: #991b1b; padding: 10px 12px; border-radius: 8px; font-size: 13px; margin-bottom: 12px; }
        label { display: block; margin-bottom: 10px; font-size: 14px; }
        label input { margin-right: 8px; }
        .actions { display: flex; gap: 12px; margin-top: 16px; }
        .btn { display: inline-block; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 14px; border: 0; cursor: pointer; text-decoration: none; }
        .btn-primary { background: #1565c0; color: #fff; }
        .btn-primary:hover { background: #0d4ea0; }
        .btn-ghost   { background: #f3f4f6; color: #4b5563; }
        .btn-ghost:hover { background: #e5e7eb; }

        /* Progress breadcrumb (matches participant/_progress.blade.php on Tailwind pages) */
        .progress { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; margin-bottom: 28px; font-size: 11px; }
        .progress .step { flex: 1; display: flex; flex-direction: column; align-items: center; min-width: 0; position: relative; }
        .progress .step .circle { width: 28px; height: 28px; border-radius: 999px; border: 2px solid #d1d5db; background: #fff; color: #9ca3af; display: flex; align-items: center; justify-content: center; font-weight: 600; }
        .progress .step .lbl { margin-top: 6px; color: #6b7280; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; }
        .progress .step.active .circle { background: #1565c0; color: #fff; border-color: #1565c0; box-shadow: 0 0 0 4px #dbeafe; }
        .progress .step.active .lbl    { color: #111827; font-weight: 600; }
        .progress .step.done .circle   { background: #16a34a; color: #fff; border-color: #16a34a; }
        .progress .step.done .lbl      { color: #15803d; }
        .progress .bar { flex: 0 0 auto; align-self: flex-start; margin-top: 14px; height: 2px; width: 100%; max-width: 60px; background: #e5e7eb; }
        .progress .step.done + .bar    { background: #4ade80; }
        @media (max-width: 720px) {
            .progress { display: none; }
            .progress-compact { display: block; font-size: 13px; color: #4b5563; margin-bottom: 16px; }
            .progress-compact strong { color: #111827; }
        }
        .progress-compact { display: none; }
    </style>
</head>
<body>
    <div class="bar-strip">
        <div class="b d"></div><div class="b i"></div><div class="b s"></div><div class="b c"></div>
    </div>
    <div class="bar-edge"></div>

    <div class="wrap">
        {{-- Progress breadcrumb — step 1 of 6 --}}
        <div class="progress-compact">Step <strong>1 of 6</strong> &mdash; <strong>Terms</strong></div>
        <ol class="progress">
            <li class="step active"><div class="circle">1</div><div class="lbl">Terms</div></li>
            <span class="bar"></span>
            <li class="step"><div class="circle">2</div><div class="lbl">Organization</div></li>
            <span class="bar"></span>
            <li class="step"><div class="circle">3</div><div class="lbl">Instructor</div></li>
            <span class="bar"></span>
            <li class="step"><div class="circle">4</div><div class="lbl">Course</div></li>
            <span class="bar"></span>
            <li class="step"><div class="circle">5</div><div class="lbl">Your Info</div></li>
            <span class="bar"></span>
            <li class="step"><div class="circle">6</div><div class="lbl">Payment</div></li>
        </ol>

        <h1>Privacy Policy &amp; Terms of Service</h1>
        <p class="sub">Please review the agreement below before continuing with participant registration.</p>

        <div class="terms" tabindex="0">
            <h2>About User Privacy</h2>
            <p>Your personal information is not used by DISC Report nor is it shared with any other party beyond your instructor(s). We do not receive credit card or bank account information; a secure third-party vendor handles all financial transactions.</p>

            <h2>Information We Collect</h2>
            <p>We collect the information you provide directly to us when you register, including name, email address, mailing address, gender, phone number, organization affiliation, instructor name, course enrollment details, and the credentials you create (username + password). We also collect your DISC assessment responses.</p>

            <h2>How We Use Your Information</h2>
            <p>Information you provide may be used to: personalize your assessment experience, share results with your designated instructor, communicate with you about your account, and improve our service. Your information will not be sold, exchanged, transferred, or given to any other company for any reason whatsoever, without your consent.</p>

            <h2>Disclaimer</h2>
            <p>DISC Report expressly disclaims any representations and warranties with respect to the assessment information. We do not assume liability for any decision made or action taken in reliance upon the information furnished.</p>

            <h2>Consent</h2>
            <p>By accepting these terms you confirm that you have read this Privacy Policy and Terms of Service and consent to the collection and use of your personal information as described above.</p>

            <p>If you have any questions regarding this policy, please email <a href="mailto:support@discreport.com">support@discreport.com</a>.</p>
        </div>

        <div class="form-card">
            @if ($errors->any())
                <div class="err">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('participant.terms.accept') }}">
                @csrf
                <label>
                    <input type="radio" name="accept" value="no" {{ old('accept', 'no') === 'no' ? 'checked' : '' }}>
                    I do <strong>not</strong> agree with the Privacy Policy and Terms of Service.
                </label>
                <label>
                    <input type="radio" name="accept" value="yes" {{ old('accept') === 'yes' ? 'checked' : '' }}>
                    I agree with the Privacy Policy and Terms of Service.
                </label>

                <div class="actions">
                    <button type="submit" class="btn btn-primary">Continue with registration</button>
                    <a href="{{ url('/') }}" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
