{{-- DISC Report — master PDF template (R-14 modular).
     Iterates only sections enabled for the participant's organization
     (R-15/R-16/R-17 via ReportSectionService). Shared section blades +
     shared CSS are used by both this PDF master and the on-screen viewer
     (reports.online) to enforce R-21 — PDF replicates on-screen layout. --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>DISC Report — {{ $result->participant?->full_name ?? 'Participant' }}</title>
<style>
@page { size: Letter; margin: 0; }
* { box-sizing: border-box; margin: 0; padding: 0; }
html, body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 10pt; color: #1f2937; line-height: 1.55; }
</style>
@include('reports._styles')
</head>
<body>
<div class="report-body">

{{-- Per-page top + bottom chrome (color bars + copyright) is supplied by
     ReportPdfService::printHeaderHtml()/printFooterHtml() via Browsershot's
     Chrome print header/footer feature — repeats on every page (R-47). --}}

@php
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

</div>
</body>
</html>
