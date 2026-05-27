<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot password &mdash; {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f3f4f6; color: #1f2937; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #fff; width: 100%; max-width: 420px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; }
        .bar { display: flex; height: 6px; }
        .bar span { flex: 1; }
        .bar .d { background: #2e7d32; } .bar .i { background: #c62828; }
        .bar .s { background: #1565c0; } .bar .c { background: #f9a825; }
        .inner { padding: 32px; }
        h1 { font-size: 22px; margin: 0 0 4px; color: #111827; }
        .sub { color: #6b7280; font-size: 14px; margin-bottom: 20px; }
        label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #374151; }
        input[type="email"] { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; margin-bottom: 14px; font-family: inherit; }
        input:focus { outline: 2px solid #1565c0; border-color: #1565c0; }
        button { width: 100%; background: #1565c0; color: #fff; border: 0; padding: 12px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
        button:hover { background: #0d4ea0; }
        .links { font-size: 13px; margin-top: 16px; color: #4b5563; text-align: center; }
        .links a { color: #1565c0; text-decoration: none; }
        .links a:hover { text-decoration: underline; }
        .ok    { background: #dcfce7; color: #166534; padding: 10px 12px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
        .error { background: #fee2e2; color: #991b1b; padding: 10px 12px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="bar"><span class="d"></span><span class="i"></span><span class="s"></span><span class="c"></span></div>
        <div class="inner">
            <h1>Forgot your password?</h1>
            <p class="sub">Enter the email address on your account. We'll send you a link to set a new password.</p>

            @if (session('status'))
                <div class="ok">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <label for="email">Email address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email" autofocus required>

                <button type="submit">Send reset link</button>
            </form>

            <div class="links">
                Remembered it? <a href="{{ route('login') }}">Sign in</a>
                &nbsp;&middot;&nbsp;
                <a href="{{ route('forgot-username') }}">Forgot username</a>
            </div>
        </div>
    </div>
</body>
</html>
