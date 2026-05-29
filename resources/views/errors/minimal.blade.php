{{-- Shared base for all branded error pages. Receives:
       $code     — HTTP status code (string)
       $title    — short headline
       $message  — body copy
       $cta      — optional [url => label]
     R-08: no stack traces, no system paths, no framework signature. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} &mdash; {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f3f4f6; color: #1f2937; min-height: 100vh; display: flex; flex-direction: column; }
        .bar { display: flex; height: 6px; }
        .bar span { flex: 1; }
        .bar .d { background: #2e7d32; }
        .bar .i { background: #c62828; }
        .bar .s { background: #1565c0; }
        .bar .c { background: #f9a825; }
        .bar-edge { height: 4px; background: #111827; }
        main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #fff; max-width: 520px; width: 100%; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); padding: 40px; text-align: center; }
        .code { font-size: 56px; font-weight: 800; color: #111827; letter-spacing: -2px; line-height: 1; margin-bottom: 12px; }
        h1 { font-size: 22px; color: #111827; margin-bottom: 8px; }
        p { color: #4b5563; font-size: 14px; line-height: 1.55; margin-bottom: 20px; }
        .btn { display: inline-block; padding: 10px 18px; background: #1565c0; color: #fff; border-radius: 8px; font-weight: 600; font-size: 14px; text-decoration: none; }
        .btn:hover { background: #0d4ea0; }
        footer { font-size: 12px; color: #9ca3af; text-align: center; padding: 16px; }
    </style>
</head>
<body>
    <div class="bar"><span class="d"></span><span class="i"></span><span class="s"></span><span class="c"></span></div>
    <div class="bar-edge"></div>

    <main>
        <div class="card">
            <div class="code">{{ $code }}</div>
            <h1>{{ $title }}</h1>
            <p>{{ $message }}</p>
            @isset($cta)
                <a href="{{ array_key_first($cta) }}" class="btn">{{ $cta[array_key_first($cta)] }}</a>
            @endisset
        </div>
    </main>

    <footer>&copy; {{ now()->format('Y') }} Spark Point Training LLC. All rights reserved.</footer>
</body>
</html>
