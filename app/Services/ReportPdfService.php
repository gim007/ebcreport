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

        return Browsershot::html($html)
            ->noSandbox()                    // required when running as non-root in containers
            ->showBackground()               // honour CSS background colors (header/footer bars)
            ->format('Letter')               // R-21 US Letter
            ->margins(0, 0, 0, 0)            // the design uses full-bleed color bars
            ->waitUntilNetworkIdle()
            ->setOption('args', ['--disable-dev-shm-usage'])
            ->pdf();
    }

    private function buildViewData(TestResult $result): array
    {
        $score           = $result->score();
        $sections        = $this->paragraphs->generate($result);
        $organization    = $this->resolveOrganization($result);
        $orgId           = $organization?->getKey();
        $enabledSections = $this->sectionService->enabledSectionsFor($orgId !== null ? (int) $orgId : null);

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
