<?php

namespace App\Services;

use App\Models\OrgSectionConfig;
use App\Models\ReportSection;
use Illuminate\Support\Collection;

/**
 * Resolves which report sections should appear, honoring R-14/R-15/R-16/R-17.
 *
 * Resolution priority for each (org, course, section_code):
 *   1. Course-specific override (org_id + course_id + section_code)
 *   2. Org-level override       (org_id + course_id IS NULL + section_code)
 *   3. ReportSection.is_active  (global default; false = section hidden
 *      entirely — used for the R-17 reserved slots S-28–S-32)
 */
class ReportSectionService
{
    /**
     * Ordered ReportSection collection for the report renderer.
     *
     * @param  int|null  $orgId     organization id (null = no overrides apply)
     * @param  int|null  $courseId  course id (null = use org-level only)
     */
    public function enabledSectionsFor(?int $orgId, ?int $courseId = null): Collection
    {
        $base = ReportSection::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        if ($orgId === null) {
            return $base;
        }

        // Pull all overrides for this org once, then resolve per section.
        $overrides = OrgSectionConfig::query()
            ->where('org_id', $orgId)
            ->where(function ($q) use ($courseId) {
                $q->whereNull('course_id');
                if ($courseId !== null) {
                    $q->orWhere('course_id', $courseId);
                }
            })
            ->get();

        // Index for fast lookup: ['S-XX' => ['org' => bool|null, 'course' => bool|null]]
        $byCode = [];
        foreach ($overrides as $o) {
            $key = $o->section_code;
            if ($o->course_id === null) {
                $byCode[$key]['org'] = $o->enabled;
            } else {
                $byCode[$key]['course'] = $o->enabled;
            }
        }

        return $base->filter(function (ReportSection $s) use ($byCode) {
            $row = $byCode[$s->code] ?? [];

            // Course override wins
            if (array_key_exists('course', $row)) {
                return (bool) $row['course'];
            }
            // Then org override
            if (array_key_exists('org', $row)) {
                return (bool) $row['org'];
            }
            // Default: enabled (because is_active=true was the base filter)
            return true;
        })->values();
    }

    /**
     * Active sections paired with current enabled state at the chosen scope.
     * Used by the Filament admin toggle pages (org-level or course-level).
     */
    public function sectionsWithOverridesFor(int $orgId, ?int $courseId = null): Collection
    {
        $query = OrgSectionConfig::query()->where('org_id', $orgId);

        if ($courseId === null) {
            $query->whereNull('course_id');
        } else {
            $query->where('course_id', $courseId);
        }

        $overrides = $query->pluck('enabled', 'section_code');

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
     * Persist toggles for one org-or-course scope. Fixes R-16 (selections were
     * reset on switching scope) by upserting one row per active section,
     * keyed on (org_id, course_id-or-null, section_code).
     *
     * @param  array<string,bool>  $toggles
     */
    public function saveOverridesFor(int $orgId, array $toggles, ?int $courseId = null): void
    {
        $activeCodes = ReportSection::query()
            ->where('is_active', true)
            ->pluck('code')
            ->all();

        foreach ($activeCodes as $code) {
            $keys = ['org_id' => $orgId, 'section_code' => $code];
            if ($courseId === null) {
                $keys['course_id'] = null;
            } else {
                $keys['course_id'] = $courseId;
            }

            OrgSectionConfig::updateOrCreate(
                $keys,
                ['enabled' => (bool) ($toggles[$code] ?? true)],
            );
        }
    }
}
