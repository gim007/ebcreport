<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'DISC Report') }}</title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background: #f9fafb; color: #1f2937; line-height: 1.55; }
        a { color: #1565c0; text-decoration: none; }
        a:hover { text-decoration: underline; }

        /* Four-color page chrome */
        .bar-strip { display: flex; height: 6pt; width: 100%; }
        .bar-strip .b { flex: 1; }
        .bar-strip .b.d { background: #2e7d32; }
        .bar-strip .b.i { background: #c62828; }
        .bar-strip .b.s { background: #1565c0; }
        .bar-strip .b.c { background: #f9a825; }
        .bar-edge { height: 4px; background: #111827; width: 100%; }

        /* Hero */
        .hero { max-width: 1080px; margin: 0 auto; padding: 56px 24px 32px; display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: center; }
        .hero-brand { display: flex; align-items: center; gap: 24px; }
        .hero-brand svg { width: 130px; height: 130px; }
        .logotype .word { font-size: 48px; font-weight: 800; letter-spacing: -1.5px; line-height: 1; color: #111827; }
        .logotype .underline { display: flex; gap: 3px; margin-top: 4px; }
        .logotype .underline span { display: block; height: 4px; width: 18px; }
        .logotype .report { font-size: 14px; letter-spacing: 8pt; color: #6b7280; margin-top: 8px; font-weight: 600; }
        .logotype .tag { font-size: 12px; color: #4b5563; margin-top: 10px; border-top: 1px solid #d1d5db; padding-top: 8px; }
        .hero-copy h1 { font-size: 28px; margin: 0 0 12px; color: #111827; letter-spacing: -0.3px; }
        .hero-copy p  { color: #4b5563; font-size: 15px; }

        /* Action cards */
        .actions { max-width: 1080px; margin: 0 auto; padding: 8px 24px 48px; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.05); padding: 24px; display: flex; flex-direction: column; }
        .card .accent { height: 4px; border-radius: 2px; margin-bottom: 16px; }
        .card.login      .accent { background: #1565c0; }
        .card.student    .accent { background: #2e7d32; }
        .card.instructor .accent { background: #f9a825; }
        .card h2 { font-size: 16px; margin: 0 0 8px; color: #111827; }
        .card p  { color: #6b7280; font-size: 13px; margin: 0 0 16px; flex: 1; }
        .card .btn { display: inline-block; background: #1565c0; color: #fff; padding: 10px 14px; border-radius: 8px; font-weight: 600; font-size: 13px; text-align: center; border: 0; cursor: pointer; font-family: inherit; }
        .card.student    .btn { background: #2e7d32; }
        .card.instructor .btn { background: #f9a825; color: #111827; }
        .card .btn:hover { opacity: 0.9; text-decoration: none; }

        /* Inline login */
        .card.login form  { display: flex; flex-direction: column; gap: 8px; }
        .card.login input { padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; font-family: inherit; }
        .card.login input:focus { outline: 2px solid #1565c0; outline-offset: 0; border-color: #1565c0; }
        .card.login input.has-error { border-color: #c62828; }
        .card.login .login-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 8px 10px; border-radius: 8px; font-size: 12px; margin-bottom: 4px; }
        .card.login .meta { font-size: 12px; color: #6b7280; margin-top: 6px; text-align: center; }

        /* Footer */
        footer { border-top: 1px solid #e5e7eb; background: #fff; margin-top: 32px; }
        .footer-inner { max-width: 1080px; margin: 0 auto; padding: 24px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; color: #6b7280; font-size: 13px; }
        .footer-inner .col-title { font-weight: 600; color: #111827; margin-bottom: 4px; }
        .footer-inner a { color: #4b5563; }
        .footer-inner a:hover { color: #1565c0; }
        .footer-inner .col:last-child { text-align: right; }
        @media (max-width: 720px) {
            .footer-inner { grid-template-columns: 1fr; }
            .footer-inner .col:last-child { text-align: left; }
        }

        @media (max-width: 720px) {
            .hero { grid-template-columns: 1fr; padding-top: 36px; gap: 24px; }
            .actions { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="bar-strip">
        <div class="b d"></div><div class="b i"></div><div class="b s"></div><div class="b c"></div>
    </div>
    <div class="bar-edge"></div>

    {{-- Hero --}}
    <section class="hero">
        <div class="hero-brand">
            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" aria-label="DISC compass">
                <path d="M100,100 L10,100 A90,90 0 0,1 100,10 Z" fill="#2e7d32"/>
                <path d="M100,100 L100,10 A90,90 0 0,1 190,100 Z" fill="#c62828"/>
                <path d="M100,100 L190,100 A90,90 0 0,1 100,190 Z" fill="#1565c0"/>
                <path d="M100,100 L100,190 A90,90 0 0,1 10,100 Z" fill="#f9a825"/>
                <line x1="100" y1="100" x2="108" y2="180" stroke="#111827" stroke-width="3"/>
                <circle cx="100" cy="100" r="9" fill="#f3f4f6" stroke="#111827" stroke-width="2"/>
            </svg>

            <div class="logotype">
                <div class="word">DISC</div>
                <div class="underline">
                    <span style="background:#2e7d32;"></span>
                    <span style="background:#c62828;"></span>
                    <span style="background:#1565c0;"></span>
                    <span style="background:#f9a825;"></span>
                </div>
                <div class="report">R E P O R T</div>
                <div class="tag">Discover.&nbsp;&nbsp;Adapt.&nbsp;&nbsp;Connect.</div>
            </div>
        </div>

        <div class="hero-copy">
            <h1>Understand your behavioral style. Lead, communicate, and connect with greater intention.</h1>
            <p>A personalized DISC assessment that maps how you naturally show up, how you adapt under pressure, and how to read and engage the people around you.</p>
        </div>
    </section>

    {{-- Three action cards: Sign in · New Participant · New Instructor --}}
    <section class="actions">
        <div class="card login">
            <div class="accent"></div>
            <h2>Sign in</h2>
            <p>Returning participants and instructors: continue where you left off.</p>
            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf
                {{-- LoginController throws ValidationException keyed on
                     `username`, so both the "credentials don't match" failure
                     and missing-field errors surface here. Other keys are
                     listed in a fallback below. --}}
                @if ($errors->any())
                    <div class="login-error" role="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                <input type="text"     name="username" placeholder="Username or email" autocomplete="username" required
                       value="{{ old('username') }}"
                       class="{{ $errors->has('username') ? 'has-error' : '' }}">
                <input type="password" name="password" placeholder="Password" autocomplete="current-password" required
                       class="{{ $errors->has('password') ? 'has-error' : '' }}">
                <button type="submit" class="btn">Sign in</button>
            </form>
            <div class="meta">
                <a href="{{ route('password.request') }}">Forgot password?</a>
                &nbsp;&middot;&nbsp;
                <a href="{{ route('forgot-username') }}">Forgot username?</a>
            </div>
        </div>

        <div class="card student">
            <div class="accent"></div>
            <h2>New Participant Registration</h2>
            <p>Take the DISC assessment for the first time. Start by choosing your organization, instructor, and course.</p>
            <a href="{{ route('participant.terms') }}" class="btn">Start participant registration</a>
        </div>

        <div class="card instructor">
            <div class="accent"></div>
            <h2>New Instructor Registration</h2>
            <p>Coaches and facilitators: create an instructor account to manage courses and view participant reports.</p>
            <a href="{{ route('instructor.register') }}" class="btn">Register as instructor</a>
        </div>
    </section>

    <footer>
        <div class="footer-inner">
            <div class="col">
                <div class="col-title">Spark Point Training LLC</div>
                <div>2100 Elmwood Ave</div>
                <div>Wilmette, IL 60091</div>
            </div>
            <div class="col">
                <div class="col-title">Contact</div>
                <div><a href="tel:+18479063472">847-906-DISC (3472)</a></div>
                <div><a href="mailto:support@discreport.com">support@discreport.com</a></div>
            </div>
            <div class="col">
                <div class="col-title">DISC Report</div>
                <div>&copy; {{ now()->format('Y') }} Spark Point Training LLC.</div>
                <div>All rights reserved.</div>
            </div>
        </div>
    </footer>
</body>
</html>
