<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Cleanup ebc_course (mirrors the earlier ebc_university cleanup):
 *
 *   - Drop `logo_image`: per-course logo was used by legacy report header;
 *     mockup R-46/R-47 standardizes a single org-level logo on the cover.
 *   - Drop `paragraphs`: legacy CSV of section IDs, now superseded by
 *     `ebc_org_section_config` (which gained a course_id column).
 *   - `course_price` varchar(255) → decimal(10,2). Currency-as-string broke
 *     sort/SUM/comparisons. Coerce non-numeric values to NULL first.
 *   - Add `is_hidden` (boolean) matching the R-38 pattern already on
 *     ebc_university and ebc_instructor. Hidden courses are filtered out of
 *     the participant selection grid.
 *   - Add `expiry_date` (nullable date) — courses past expiry stop accepting
 *     new participant registrations. Legacy used `today + 300 days` default
 *     non-null; we make it nullable so "never expires" is expressible.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ebc_course', function (Blueprint $table) {
            if (Schema::hasColumn('ebc_course', 'logo_image')) {
                $table->dropColumn('logo_image');
            }
            if (Schema::hasColumn('ebc_course', 'paragraphs')) {
                $table->dropColumn('paragraphs');
            }
        });

        DB::statement("UPDATE ebc_course SET course_price = NULL WHERE course_price IS NOT NULL AND course_price NOT REGEXP '^-?[0-9]+(\\.[0-9]+)?$'");
        DB::statement('ALTER TABLE ebc_course MODIFY course_price DECIMAL(10, 2) NULL');

        Schema::table('ebc_course', function (Blueprint $table) {
            if (! Schema::hasColumn('ebc_course', 'is_hidden')) {
                $table->boolean('is_hidden')->default(false)->after('course_price');
            }
            if (! Schema::hasColumn('ebc_course', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('is_hidden');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ebc_course', function (Blueprint $table) {
            $table->dropColumn(['is_hidden', 'expiry_date']);
            if (! Schema::hasColumn('ebc_course', 'logo_image')) {
                $table->string('logo_image', 255)->nullable();
            }
            if (! Schema::hasColumn('ebc_course', 'paragraphs')) {
                $table->text('paragraphs')->nullable();
            }
        });
        DB::statement('ALTER TABLE ebc_course MODIFY course_price VARCHAR(255) NULL');
    }
};
