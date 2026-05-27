<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify your email &mdash; {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f9fafb; color: #1f2937; line-height: 1.55; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .bar-strip { position: fixed; top: 0; left: 0; right: 0; display: flex; height: 6pt; }
        .bar-strip .b { flex: 1; }
        .bar-strip .b.d { background: #2e7d32; }
        .bar-strip .b.i { background: #c62828; }
        .bar-strip .b.s { background: #1565c0; }
        .bar-strip .b.c { background: #f9a825; }
        .bar-edge { position: fixed; top: 6pt; left: 0; right: 0; height: 4px; background: #111827; }
        .card { background: #fff; max-width: 480px; width: 100%; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); padding: 32px; text-align: center; }
        h1 { font-size: 22px; margin: 0 0 8px; color: #111827; }
        p  { color: #4b5563; margin: 0 0 16px; }
        .ok { background: #ecfdf5; color: #166534; padding: 10px 12px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
        .btn { display: inline-block; padding: 10px 18px; background: #1565c0; color: #fff; border: 0; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; }
        .btn:hover { background: #0d4ea0; }
        .meta { font-size: 13px; color: #6b7280; margin-top: 16px; }
        .meta a { color: #1565c0; }
    </style>
</head>
<body>
    <div class="bar-strip"><div class="b d"></div><div class="b i"></div><div class="b s"></div><div class="b c"></div></div>
    <div class="bar-edge"></div>

    <div class="card">
        <h1>Verify your email address</h1>

        @if (session('status'))
            <div class="ok">{{ session('status') }}</div>
        @endif

        <p>Thanks for registering. We sent a verification link to <strong>{{ auth()->user()?->user_email }}</strong>. Click it to confirm your address.</p>
        <p>If it didn't arrive, check spam or request another link.</p>

        <form method="POST" action="{{ route('instructor.verify.resend') }}">
            @csrf
            <button type="submit" class="btn">Resend verification email</button>
        </form>

        <div class="meta">
            Wrong account?
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" style="background:none; border:0; color:#1565c0; cursor:pointer; padding:0; font:inherit;">Sign out</button>
            </form>
        </div>
    </div>
</body>
</html>
