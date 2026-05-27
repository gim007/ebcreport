<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\TestResult;
use Illuminate\Support\Facades\View;
use Spatie\Browsershot\Browsershot;

/**
 * Renders the DISC report via Browsershot (headless Chromium).
 * Chrome's CSS engine matches the SOW mockup's design language directly —
 * SVG, flexbox/grid, border-radius, real font rendering all work as designed.
 */
class ReportPdfService
{
    public function __construct(
        private readonly DiscParagraphService $paragraphs,
        private readonly DiscChartService     $charts,
        private readonly ReportSectionService $sectionService,
    ) {}

    /** Returns the rendered report PDF as a raw byte string. */
    public function generate(TestResult $result): string
    {
        $html = View::make('reports.pdf', $this->buildViewData($result))->render();

        // Use Chrome's print header/footer HTML so the four-color bars + copyright
        // repeat on EVERY page (including continuations from page-break-inside).
        // Content margins (top/bottom) give that chrome room.
        return Browsershot::html($html)
            ->noSandbox()
            ->showBackground()
            ->format('Letter')                           // R-21 US Letter
            ->margins(14, 0, 22, 0)                      // top ~40pt / bottom ~62pt
            ->showBrowserHeaderAndFooter()
            ->headerHtml($this->printHeaderHtml())
            ->footerHtml($this->printFooterHtml())
            ->waitUntilNetworkIdle()
            ->setOption('args', ['--disable-dev-shm-usage'])
            ->pdf();
    }

    /** Per-page top chrome (R-47): four-color bar strip rendered on every PDF page.
     *  Chrome's print header strips most CSS — we use inline SVG which always
     *  renders, with `width:100%` on the wrapper to span the full page width. */
    private function printHeaderHtml(): string
    {
        return '<div style="width:100%; -webkit-print-color-adjust:exact; print-color-adjust:exact;">'
            . '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="8" preserveAspectRatio="none" viewBox="0 0 100 8">'
            . '<rect x="0"  y="0" width="25" height="6" fill="#2e7d32"/>'
            . '<rect x="25" y="0" width="25" height="6" fill="#c62828"/>'
            . '<rect x="50" y="0" width="25" height="6" fill="#1565c0"/>'
            . '<rect x="75" y="0" width="25" height="6" fill="#f9a825"/>'
            . '</svg>'
            . '</div>';
    }

    /** Per-page bottom chrome (R-47): copyright + dark edge + four-color bar strip. */
    private function printFooterHtml(): string
    {
        $year = date('Y');
        return '<div style="width:100%; font-family:Helvetica,Arial,sans-serif; -webkit-print-color-adjust:exact; print-color-adjust:exact;">'
            . '<div style="text-align:center; font-size:8pt; color:#6b7280; padding:0 0 4pt;">'
            . '&copy; ' . $year . ' Spark Point Training LLC. All rights reserved.'
            . '</div>'
            . '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="12" preserveAspectRatio="none" viewBox="0 0 100 12">'
            . '<rect x="0"  y="0" width="25" height="6" fill="#2e7d32"/>'
            . '<rect x="25" y="0" width="25" height="6" fill="#c62828"/>'
            . '<rect x="50" y="0" width="25" height="6" fill="#1565c0"/>'
            . '<rect x="75" y="0" width="25" height="6" fill="#f9a825"/>'
            . '<rect x="0"  y="6" width="100" height="4" fill="#111827"/>'
            . '</svg>'
            . '</div>';
    }

    private function buildViewData(TestResult $result): array
    {
        $score           = $result->score();
        $sections        = $this->paragraphs->generate($result);
        $organization    = $this->resolveOrganization($result);
        $orgId           = $organization?->getKey();
        $courseId        = $result->course_id !== null ? (int) $result->course_id : null;
        $enabledSections = $this->sectionService->enabledSectionsFor(
            $orgId !== null ? (int) $orgId : null,
            $courseId,
        );

        return [
            'result'           => $result,
            'score'            => $score,
            'sections'         => $sections,
            'enabledSections'  => $enabledSections,
            'charts'           => [
                'mask'    => $this->charts->verticalProfileChart($score->maskPercentile,   'Mask'),
                'shift'   => $this->charts->shiftChart($score->shift()),
                'latent'  => $this->charts->verticalProfileChart($score->latentPercentile, 'Latent'),
                'tension' => $this->charts->tensionBars($score),
                'compass' => $this->charts->discCompass(160),
            ],
            'organization' => $organization,
            'logoData'     => $this->resolveLogoData($organization),
            'snapshotTags' => $this->snapshotTags($score),
        ];
    }

    private function resolveOrganization(TestResult $result): ?Organization
    {
        return $result->course?->instructor?->organization;
    }

    /**
     * R-18 / R-19: load the org logo as a base64 data URI so the PDF works
     * without an external HTTP fetch. Returns null cleanly when no logo is
     * on file — the cover renders without a placeholder in that case.
     */
    private function resolveLogoData(?Organization $organization): ?string
    {
        if ($organization === null) {
            return null;
        }

        $media = $organization->getFirstMedia('logo');
        if ($media === null) {
            return null;
        }

        $path = $media->getPath();
        if (! is_file($path)) {
            return null;
        }

        $bytes = @file_get_contents($path);
        if ($bytes === false) {
            return null;
        }

        $mime = $media->mime_type ?: 'image/png';
        return 'data:' . $mime . ';base64,' . base64_encode($bytes);
    }

    /** S-05 "Snapshot of Your Style" — keyword tags by dominant Mask dimension. */
    private function snapshotTags(DiscScore $score): array
    {
        return match ($score->dominantLabel()) {
            'D' => ['Results-Driven', 'Direct & Bold', 'Decisive', 'Competitive', 'Action-Oriented'],
            'I' => ['Enthusiastic', 'Persuasive', 'Outgoing', 'Optimistic', 'People-Focused'],
            'S' => ['Patient', 'Loyal', 'Steady', 'Supportive', 'Collaborative'],
            'C' => ['Analytical', 'Precise', 'Systematic', 'Detail-Oriented', 'Quality-Focused'],
            default => ['Balanced', 'Adaptable', 'Versatile', 'Measured', 'Even-Handed'],
        };
    }
}
