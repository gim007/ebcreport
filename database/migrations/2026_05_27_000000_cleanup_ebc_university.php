<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Cleanup ebc_university:
 *   - Drop `logo_image`: legacy column, superseded by `logo_path` + Spatie Media.
 *   - Drop `paragraphs`: legacy per-org section CSV, superseded by
 *     `ebc_org_section_config` (R-15).
 *   - Drop `headlines`: legacy custom cover-title text. The new mockup
 *     (R-46/R-47) standardizes the cover to "DISC Report / Discover. Adapt.
 *     Connect." for every org with the org logo. Per-org headline overrides
 *     are no longer part of the design.
 *   - Convert `course_price` from `varchar(255)` to `decimal(10,2)`. Currency
 *     was stored as a string, breaking sort/sum/comparison; fix the type.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Drop dead columns one at a time (Schema::dropColumn batches don't
        // always work cleanly on MySQL when paired with type changes).
        Schema::table('ebc_university', function (Blueprint $table) {
            if (Schema::hasColumn('ebc_university', 'logo_image')) {
                $table->dropColumn('logo_image');
            }
            if (Schema::hasColumn('ebc_university', 'paragraphs')) {
                $table->dropColumn('paragraphs');
            }
        });

        // Course price: varchar -> decimal. Coerce non-numeric values to NULL
        // before the type change so the conversion doesn't fail on bad data.
        DB::statement("UPDATE ebc_university SET course_price = NULL WHERE course_price IS NOT NULL AND course_price NOT REGEXP '^-?[0-9]+(\\.[0-9]+)?$'");
        DB::statement('ALTER TABLE ebc_university MODIFY course_price DECIMAL(10, 2) NULL');
    }

    public function down(): void
    {
        Schema::table('ebc_university', function (Blueprint $table) {
            if (! Schema::hasColumn('ebc_university', 'logo_image')) {
                $table->string('logo_image', 255)->nullable();
            }
            if (! Schema::hasColumn('ebc_university', 'paragraphs')) {
                $table->text('paragraphs')->nullable();
            }
        });
        DB::statement('ALTER TABLE ebc_university MODIFY course_price VARCHAR(255) NULL');
    }
};
