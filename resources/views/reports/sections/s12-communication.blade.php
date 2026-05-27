<div class="page">

    <h2 style="border-left-color:#1565c0;">Communication Preferences</h2>

    <p>How <strong>{{ $result->participant?->stud_fname ?? 'this participant' }}</strong> communicates — and how they prefer others to communicate with them — is one of the most immediate expressions of their behavioral style. Getting this right accelerates trust, reduces friction, and makes every interaction more productive.</p>

    <h3 style="color:#1565c0;">HOW {{ strtoupper($result->participant?->stud_fname ?? 'THEY') }} TEND TO COMMUNICATE</h3>
    <p>{!! $sections['interpersonal'] !!}</p>

    <h3 style="color:#1565c0; margin-top:14pt;">CONNECTING WITH OTHERS</h3>
    <p>{!! $sections['connecting'] !!}</p>

    <div class="callout" style="border-left-color:#f9a825;">
        <span class="lbl" style="color:#b8860b;">A NOTE ON FLEXIBILITY</span>
        Communication style is not fixed. These natural preferences are a starting point, not a ceiling. Small adjustments in sequencing, tone, and pacing can significantly change how messages are received.
    </div>
</div>
