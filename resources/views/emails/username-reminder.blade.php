<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: sans-serif; color: #333;">
    <h2>Your DISC Report Username(s)</h2>
    <p>You requested a username reminder. The following username(s) are registered to your email address:</p>
    <ul>
        @foreach ($users as $user)
            <li><strong>{{ $user->user_login_id }}</strong></li>
        @endforeach
    </ul>
    <p>If you did not request this, you can safely ignore this email.</p>
    <p>— The DISC Report Team</p>
</body>
</html>
