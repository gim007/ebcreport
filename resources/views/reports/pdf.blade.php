{{-- DISC Report — master PDF template (R-14 modular).
     Iterates only sections enabled for the participant's organization
     (R-15/R-16/R-17 via ReportSectionService). Section-to-blade mapping
     lives in resources/views/reports/partials/section-map.blade.php. --}}
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

/* Each section is its own page. `min-height: 100vh` causes Chrome to
   eject an extra blank page when content + padding exceeds the viewport,
   so we let content size naturally — the absolute footer bar provides
   the visual page bottom regardless of content length. */
.page { position: relative; padding: 56pt 64pt 80pt 64pt; page-break-after: always; }
.page:last-of-type { page-break-after: auto; }

/* Four-color bars on every page (R-47): rendered as flex strips, dark edge below. */
.bar-strip { display: flex; height: 6pt; width: 100%; }
.bar-strip .b { flex: 1; }
.bar-strip .b.d { background: #2e7d32; }
.bar-strip .b.i { background: #c62828; }
.bar-strip .b.s { background: #1565c0; }
.bar-strip .b.c { background: #f9a825; }
.bar-edge { height: 4pt; background: #111827; width: 100%; }

.page-top    { position: absolute; top: 0;    left: 0; right: 0; }
.page-bottom { position: absolute; bottom: 0; left: 0; right: 0; }
.page-bottom .meta { text-align: center; font-size: 8pt; color: #6b7280; padding: 8pt 0 10pt; }

/* Section headers — mockup: colored left accent bar + bold heading */
h1 { font-size: 30pt; color: #111827; font-weight: 700; margin-bottom: 6pt; letter-spacing: -0.3pt; }
h2 { font-size: 22pt; color: #111827; font-weight: 700; padding-left: 12pt; border-left: 6pt solid #2e7d32; margin-bottom: 16pt; letter-spacing: -0.2pt; }
h3 { font-size: 11pt; color: #1f2937; font-weight: 700; margin: 14pt 0 6pt; letter-spacing: 0.6pt; }

p  { margin-bottom: 10pt; }
ul, ol { margin: 0 0 10pt 18pt; }

/* DISC dimension colors (mockup palette) */
.dim-d, .label-d { color: #2e7d32; }
.dim-i, .label-i { color: #c62828; }
.dim-s, .label-s { color: #1565c0; }
.dim-c, .label-c { color: #f9a825; }

/* Cover (R-47) — mockup layout */
.cover { padding: 80pt 70pt; min-height: 100vh; display: flex; flex-direction: column; }
.cover-hero { display: flex; align-items: center; gap: 28pt; margin-top: 40pt; }
.cover-compass svg { display: block; width: 200pt; height: 200pt; }
.cover-logotype .word { font-size: 64pt; font-weight: 800; color: #1f2937; letter-spacing: -2pt; line-height: 1; }
.cover-logotype .underline { display: flex; gap: 3pt; margin-top: 6pt; }
.cover-logotype .underline span { display: block; height: 5pt; width: 22pt; }
.cover-logotype .report { font-size: 20pt; letter-spacing: 12pt; color: #6b7280; margin-top: 10pt; font-weight: 600; }
.cover-logotype .tag { font-size: 14pt; color: #4b5563; margin-top: 14pt; border-top: 1pt solid #d1d5db; padding-top: 10pt; }
.cover-prepared { font-size: 11pt; letter-spacing: 5pt; color: #6b7280; margin-top: 100pt; font-weight: 600; }
.cover-name { font-size: 38pt; font-weight: 800; color: #111827; margin-top: 6pt; letter-spacing: -0.5pt; }
.cover-date { font-size: 14pt; color: #4b5563; margin-top: 10pt; }
.cover-logo-slot { margin-top: 60pt; }
.cover-logo-slot img { max-width: 240pt; max-height: 80pt; }

/* Two-column data tables (Strengths, Blind Spots, etc.) */
.kv { width: 100%; border-collapse: collapse; margin: 8pt 0; }
.kv td { padding: 8pt 10pt; vertical-align: top; border-bottom: 1pt solid #e5e7eb; }
.kv td.k { width: 32%; font-weight: bold; }
.kv.green td.k { color: #2e7d32; }
.kv.red td.k   { color: #c62828; }
.kv.blue td.k  { color: #1565c0; }
.kv.gold td.k  { color: #b8860b; }

/* Tip/Note callouts */
.callout { background: #f9fafb; border-left: 4pt solid #1565c0; padding: 8pt 12pt; margin: 10pt 0; font-size: 9.5pt; }
.callout .lbl { color: #1565c0; font-weight: bold; letter-spacing: 1pt; font-size: 9pt; display: block; margin-bottom: 2pt; }

/* S-05 three-chart grid */
.scoring-grid { width: 100%; border-collapse: separate; border-spacing: 8pt 0; }
.scoring-grid td { vertical-align: top; width: 33.33%; }
.scoring-grid .chart-title { text-align: center; font-weight: bold; font-size: 13pt; margin-bottom: 0; color: #111827; }
.scoring-grid .chart-sub { text-align: center; font-size: 9pt; color: #6b7280; margin-bottom: 6pt; }
.scoring-grid .chart-cap { padding: 8pt 10pt; font-size: 9pt; line-height: 1.45; background: #f9fafb; border-top: 3pt solid #2e7d32; margin-top: 6pt; }
.scoring-grid td.shift .chart-cap { border-top-color: #1565c0; }
.scoring-grid td.latent .chart-cap { border-top-color: #f9a825; }

/* Snapshot tags row */
.snapshot { margin-top: 14pt; background: #1f2937; color: #fff; padding: 10pt 14pt; }
.snapshot .h { font-weight: bold; text-decoration: underline; color: #f9a825; margin-bottom: 6pt; display: block; }
.snapshot .tag { display: inline-block; padding: 4pt 10pt; font-size: 9pt; font-weight: bold; border-right: 1pt solid #374151; }
.snapshot .tag:last-child { border-right: 0; }

/* Working With sub-sections (S-18–S-21) */
.working-section { margin-bottom: 14pt; padding-left: 10pt; border-left: 4pt solid #1565c0; }
.working-section.d { border-left-color: #2e7d32; }
.working-section.i { border-left-color: #c62828; }
.working-section.s { border-left-color: #1565c0; }
.working-section.c { border-left-color: #f9a825; }
</style>
</head>
<body>

@php
    // Map section code -> blade view to render. Inactive/unmapped codes are simply skipped.
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

</body>
</html>
