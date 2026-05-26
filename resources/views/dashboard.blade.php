<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Dashboard — DISC Report</title></head>
<body>
    <h1>DISC Report Dashboard</h1>
    <p>Welcome, {{ Auth::user()->user_login_id ?? 'Participant' }}.</p>
    <a href="{{ url('/logout') }}">Log out</a>
</body>
</html>
