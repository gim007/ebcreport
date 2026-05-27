<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset password &mdash; {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f3f4f6; color: #1f2937; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #fff; width: 100%; max-width: 460px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; }
        .bar { display: flex; height: 6px; }
        .bar span { flex: 1; }
        .bar .d { background: #2e7d32; } .bar .i { background: #c62828; }
        .bar .s { background: #1565c0; } .bar .c { background: #f9a825; }
        .inner { padding: 32px; }
        h1 { font-size: 22px; margin: 0 0 4px; color: #111827; }
        .sub { color: #6b7280; font-size: 14px; margin-bottom: 20px; }
        label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #374151; }
        input[type="email"], input[type="password"] { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; margin-bottom: 14px; font-family: inherit; }
        input:focus { outline: 2px solid #1565c0; border-color: #1565c0; }
        button { width: 100%; background: #1565c0; color: #fff; border: 0; padding: 12px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
        button:hover { background: #0d4ea0; }
        .error { background: #fee2e2; color: #991b1b; padding: 10px 12px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
        .hint { color: #6b7280; font-size: 12px; margin-top: -8px; margin-bottom: 14px; }
        .links { font-size: 13px; margin-top: 16px; color: #4b5563; text-align: center; }
        .links a { color: #1565c0; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card">
        <div class="bar"><span class="d"></span><span class="i"></span><span class="s"></span><span class="c"></span></div>
        <div class="inner">
            <h1>Set a new password</h1>
            <p class="sub">Choose a new password for your account.</p>

            @if ($errors->any())
                <div class="error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <label for="email">Email address</label>
                <input type="email" id="email" name="email" value="{{ old('email', $email) }}" autocomplete="email" required>

                <label for="password">New password</label>
                <input type="password" id="password" name="password" autocomplete="new-password" required minlength="8">
                <p class="hint">At least 8 characters.</p>

                <label for="password_confirmation">Confirm new password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required minlength="8">

                <button type="submit">Reset password</button>
            </form>

            <div class="links">
                <a href="{{ route('login') }}">Back to sign in</a>
            </div>
        </div>
    </div>
</body>
</html>
