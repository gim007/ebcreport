<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * R-15 full compliance: per-course section overrides in addition to per-org.
 *
 * SOW R-15: "For each organization/course, an administrator must be able to
 * toggle each report section on or off." The legacy site stored these on
 * `ebc_course.paragraphs` (course-level) with `ebc_university.paragraphs`
 * as a default seed.
 *
 * Resolution order at report-generation time (implemented in
 * ReportSectionService::enabledSectionsFor()):
 *   1. Course-specific row: org_id + course_id + section_code
 *   2. Org-level row:       org_id + course_id IS NULL + section_code
 *   3. ReportSection.is_active default (when no override exists)
 *
 * `course_id` is nullable; existing org-level rows keep working unchanged.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ebc_org_section_config', function (Blueprint $table) {
            $table->unsignedInteger('course_id')->nullable()->after('org_id');
            $table->dropUnique(['org_id', 'section_code']);
            // Composite unique now includes course_id; MySQL treats NULL
            // course_id values as distinct, so multiple org-level rows would
            // theoretically be allowed by the UNIQUE alone — the service
            // layer enforces "one row per (org_id, course_id-or-null, code)"
            // via updateOrCreate.
            $table->unique(['org_id', 'course_id', 'section_code'], 'org_course_section_unique');
            $table->index('course_id');
        });
    }

    public function down(): void
    {
        Schema::table('ebc_org_section_config', function (Blueprint $table) {
            $table->dropUnique('org_course_section_unique');
            $table->dropIndex(['course_id']);
            $table->dropColumn('course_id');
            $table->unique(['org_id', 'section_code']);
        });
    }
};
