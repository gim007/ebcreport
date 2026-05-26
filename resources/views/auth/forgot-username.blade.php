<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Forgot Username — DISC Report</title>
</head>
<body>
    <h2>Forgot Your Username?</h2>

    @if (session('status'))
        <p style="color: green;">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('forgot-username.send') }}">
        @csrf
        <label>Email address<br>
            <input type="email" name="email" required value="{{ old('email') }}">
        </label>
        @error('email')<p style="color:red;">{{ $message }}</p>@enderror
        <br><button type="submit">Send Username Reminder</button>
    </form>

    <p><a href="{{ url('/') }}">Back to login</a></p>
</body>
</html>
