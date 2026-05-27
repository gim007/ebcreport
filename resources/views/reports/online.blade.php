{{-- DISC Report — on-screen viewer (R-21: same layout as PDF, browser-styled).
     Iterates ReportSectionService::enabledSectionsFor() so per-org section
     toggles (R-15) apply on-screen exactly as they do in the PDF. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DISC Report &mdash; {{ $result->participant?->full_name ?? 'Participant' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 10pt; color: #1f2937; line-height: 1.55; background: #f3f4f6; min-height: 100vh; }
        a { color: #1565c0; text-decoration: none; }
        a:hover { text-decoration: underline; }

        /* Sticky top action bar */
        .toolbar { position: sticky; top: 0; z-index: 50; background: #fff; border-bottom: 1px solid #e5e7eb; }
        .toolbar .bar-strip { height: 6px; }
        .toolbar .inner { display: flex; align-items: center; justify-content: space-between; max-width: 900px; margin: 0 auto; padding: 12px 24px; }
        .toolbar .title { font-size: 14px; color: #6b7280; }
        .toolbar .title strong { color: #111827; }
        .toolbar .actions { display: flex; gap: 12px; }
        .toolbar .btn { display: inline-block; padding: 8px 14px; border-radius: 8px; font-weight: 600; font-size: 13px; font-family: inherit; cursor: pointer; border: 0; }
        .toolbar .btn-primary { background: #1565c0; color: #fff; }
        .toolbar .btn-primary:hover { background: #0d4ea0; text-decoration: none; }
        .toolbar .btn-ghost { background: #f3f4f6; color: #4b5563; }

        /* Report shell — each .page becomes a paper-style card on screen */
        .shell { max-width: 900px; margin: 24px auto; padding: 0 12px 48px; }
        .report-body .page {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            padding: 36px 48px;
            margin-bottom: 18px;
            page-break-after: auto;  /* override the print value */
            position: relative;
        }

        /* Mobile: tighter padding, no cards (just flow) */
        @media (max-width: 720px) {
            .report-body .page { padding: 24px 18px; border-radius: 8px; margin-bottom: 12px; }
            .toolbar .inner { padding: 10px 14px; flex-direction: column; gap: 8px; align-items: stretch; }
        }
    </style>
    @include('reports._styles')
</head>
<body>

<header class="toolbar">
    <div class="bar-strip">
        <div class="b d"></div><div class="b i"></div><div class="b s"></div><div class="b c"></div>
    </div>
    <div class="inner">
        <div class="title">
            <strong>DISC Report</strong>
            &mdash; {{ $result->participant?->full_name ?? 'Participant' }}
            &middot; {{ ($result->result_date ?? now())->format('M j, Y') }}
        </div>
        <div class="actions">
            @if ($backUrl ?? false)
                <a href="{{ $backUrl }}" class="btn btn-ghost">&larr; Back</a>
            @endif
            <a href="{{ $downloadUrl }}" class="btn btn-primary">Download PDF</a>
        </div>
    </div>
</header>

<main class="shell report-body">

@php
    // Same map the PDF master uses — R-21 parity.
    $sectionMap = [
        'S-01' => 'reports.sections.s01-cover',
        'S-02' => 'reports.sections.s02-introduction',
        'S-03' => 'reports.sections.s03-how-to-use',
        'S-04' => 'reports.sections.s04-disc-model',
        'S-05' => 'reports.sections.s05-scoring-summary',
        'S-06' => 'reports.sections.s06-profile-normal',
        'S-07' => 'reports.sections.s07-profile-stress',
        'S-08' => 'reports.sections.s08-overview',
        'S-09' => 'reports.sections.s09-motivating-factors',
        'S-10' => 'reports.sections.s10-strengths',
        'S-11' => 'reports.sections.s11-blind-spots',
        'S-12' => 'reports.sections.s12-communication',
        'S-13' => 'reports.sections.s13-decision-making',
        'S-14' => 'reports.sections.s14-pressure-behavior',
        'S-15' => 'reports.sections.s15-conflict-style',
        'S-16' => 'reports.sections.s16-others-perception',
        'S-17' => 'reports.sections.s17-profile-tensions',
        'S-18' => 'reports.sections.s18-working-with-d',
        'S-19' => 'reports.sections.s19-working-with-i',
        'S-20' => 'reports.sections.s20-working-with-s',
        'S-21' => 'reports.sections.s21-working-with-c',
        'S-22' => 'reports.sections.s22-where-to-go',
        'S-23' => 'reports.sections.s23-glossary',
    ];
@endphp

@foreach ($enabledSections as $section)
    @if (isset($sectionMap[$section->code]) && view()->exists($sectionMap[$section->code]))
        @include($sectionMap[$section->code])
    @endif
@endforeach

</main>

</body>
</html>
