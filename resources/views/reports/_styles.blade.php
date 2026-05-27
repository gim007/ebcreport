{{-- Shared report styles used by BOTH the PDF master (reports.pdf) and the
     on-screen viewer (reports.online). Keeping them in one file enforces
     R-21 — "PDF output must replicate the on-screen report layout". --}}
<style>
{{-- Section page block — in PDF this is a full-page slab with page-break-after.
     On-screen we override .page below to use card spacing instead.       --}}
.page { position: relative; padding: 16pt 64pt 16pt 64pt; page-break-after: always; }
.page:last-of-type { page-break-after: auto; }

/* Four-color bar strip (used inline by S-01 cover) */
.bar-strip { display: flex; height: 6pt; width: 100%; }
.bar-strip .b { flex: 1; }
.bar-strip .b.d { background: #2e7d32; }
.bar-strip .b.i { background: #c62828; }
.bar-strip .b.s { background: #1565c0; }
.bar-strip .b.c { background: #f9a825; }
.bar-edge { height: 4pt; background: #111827; width: 100%; }

/* Section headers — mockup: colored left accent bar */
.report-body h1 { font-size: 30pt; color: #111827; font-weight: 700; margin-bottom: 6pt; letter-spacing: -0.3pt; }
.report-body h2 { font-size: 22pt; color: #111827; font-weight: 700; padding-left: 12pt; border-left: 6pt solid #2e7d32; margin-bottom: 16pt; letter-spacing: -0.2pt; }
.report-body h3 { font-size: 11pt; color: #1f2937; font-weight: 700; margin: 14pt 0 6pt; letter-spacing: 0.6pt; }
.report-body p  { margin-bottom: 10pt; }
.report-body ul, .report-body ol { margin: 0 0 10pt 18pt; }

/* DISC dimension colors (mockup palette) */
.dim-d, .label-d { color: #2e7d32; }
.dim-i, .label-i { color: #c62828; }
.dim-s, .label-s { color: #1565c0; }
.dim-c, .label-c { color: #f9a825; }

/* Cover (R-47) */
.cover { padding: 80pt 70pt 40pt 70pt; }
.cover-hero { display: flex; align-items: center; gap: 28pt; margin-top: 40pt; }
.cover-compass svg { display: block; width: 200pt; height: 200pt; }
.cover-logotype .word { font-size: 64pt; font-weight: 800; color: #1f2937; letter-spacing: -2pt; line-height: 1; }
.cover-logotype .underline { display: flex; gap: 3pt; margin-top: 6pt; }
.cover-logotype .underline span { display: block; height: 5pt; width: 22pt; }
.cover-logotype .report { font-size: 18pt; letter-spacing: 9pt; color: #6b7280; margin-top: 10pt; font-weight: 600; white-space: nowrap; }
.cover-logotype .tag { font-size: 14pt; color: #4b5563; margin-top: 14pt; border-top: 1pt solid #d1d5db; padding-top: 10pt; }
.cover-prepared { font-size: 11pt; letter-spacing: 5pt; color: #6b7280; margin-top: 100pt; font-weight: 600; }
.cover-name { font-size: 38pt; font-weight: 800; color: #111827; margin-top: 6pt; letter-spacing: -0.5pt; }
.cover-date { font-size: 14pt; color: #4b5563; margin-top: 10pt; }
.cover-logo-slot { margin-top: 60pt; }
.cover-logo-slot img { max-width: 240pt; max-height: 80pt; }

/* Two-column data tables */
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

/* Working With sub-sections */
.working-section { margin-bottom: 14pt; padding-left: 10pt; border-left: 4pt solid #1565c0; }
.working-section.d { border-left-color: #2e7d32; }
.working-section.i { border-left-color: #c62828; }
.working-section.s { border-left-color: #1565c0; }
.working-section.c { border-left-color: #f9a825; }
</style>
