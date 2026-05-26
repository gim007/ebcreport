<?php

namespace App\Services;

use App\Models\OrgSectionConfig;
use App\Models\ReportSection;
use Illuminate\Support\Collection;

/**
 * Returns the ordered list of report sections to render for a given organization.
 *
 * R-14: modular sections — the PDF template iterates this list.
 * R-15: per-organization overrides — OrgSectionConfig flips a section on/off for one org.
 * R-17: inactive global sections (is_active=false on ReportSection) are hidden from both
 *       the rendered report and the admin section toggles, until activated.
 */
class ReportSectionService
{
    /**
     * Ordered ReportSection collection that should appear in the report.
     * Default is "enabled" — only explicit per-org overrides disable a section.
     */
    public function enabledSectionsFor(?int $orgId): Collection
    {
        $base = ReportSection::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        if ($orgId === null) {
            return $base;
        }

        $overrides = OrgSectionConfig::query()
            ->where('org_id', $orgId)
            ->pluck('enabled', 'section_code');

        return $base->filter(fn (ReportSection $s) => $overrides->get($s->code, true))->values();
    }

    /**
     * Every active section, paired with whether it is enabled for $orgId.
     * Used by the Filament SectionConfigPage to render the toggle list.
     */
    public function sectionsWithOverridesFor(int $orgId): Collection
    {
        $overrides = OrgSectionConfig::query()
            ->where('org_id', $orgId)
            ->pluck('enabled', 'section_code');

        return ReportSection::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (ReportSection $s) => [
                'code'    => $s->code,
                'name'    => $s->name,
                'enabled' => (bool) $overrides->get($s->code, true),
            ]);
    }

    /**
     * Persist toggles for one organization. Fixes R-16 (selections were reset on
     * switching orgs) by writing one OrgSectionConfig row per active section,
     * scoped to a single org_id.
     *
     * @param  array<string,bool>  $toggles  ['S-01' => true, 'S-02' => false, ...]
     */
    public function saveOverridesFor(int $orgId, array $toggles): void
    {
        $activeCodes = ReportSection::query()
            ->where('is_active', true)
            ->pluck('code')
            ->all();

        foreach ($activeCodes as $code) {
            OrgSectionConfig::updateOrCreate(
                ['org_id' => $orgId, 'section_code' => $code],
                ['enabled' => (bool) ($toggles[$code] ?? true)],
            );
        }
    }
}
