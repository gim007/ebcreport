<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family:sans-serif;color:#374151;max-width:600px;margin:0 auto;padding:24px;">
<h2 style="color:#1d4ed8;">Your DISC Report is Ready</h2>
<p>Hello {{ $result->participant->stud_fname ?? 'there' }},</p>
<p>Thank you for completing the DISC personality assessment. Your personalized report is attached to this email as a PDF.</p>
<p>The report covers your DISC profile across several dimensions including:</p>
<ul>
    <li>Profile Overview</li>
    <li>Motivating Factors</li>
    <li>Strengths &amp; Advantages</li>
    <li>Communication Preferences</li>
    <li>Decision-Making Style</li>
    <li>Behavior Under Pressure</li>
    <li>Working with Different Styles</li>
</ul>
<p>You can also view your report online at any time by signing in to your account.</p>
<hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0;">
<p style="font-size:12px;color:#9ca3af;">This message was generated automatically. Please do not reply to this email.</p>
</body>
</html>
