<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add legacy contact + mailing-address fields back to ebc_student.
 *
 * R-31: phone is required when SMS recovery is used (optional at registration).
 * Address / city / state / zip / country: legacy parity (the original
 * student_registration.php form collected these and admins/instructors rely
 * on the data for contact and reporting).
 *
 * All columns nullable so existing rows continue to work.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ebc_student', function (Blueprint $table) {
            $table->string('stud_phone',   50)->nullable()->after('stud_email');
            $table->string('stud_address', 200)->nullable()->after('stud_phone');
            $table->string('stud_city',    100)->nullable()->after('stud_address');
            $table->string('stud_state',   100)->nullable()->after('stud_city');   // 2-letter for US, free-text for intl (R-35)
            $table->string('stud_zip',     20)->nullable()->after('stud_state');
            $table->string('stud_country', 2)->nullable()->after('stud_zip');     // ISO-3166 alpha-2
        });
    }

    public function down(): void
    {
        Schema::table('ebc_student', function (Blueprint $table) {
            $table->dropColumn(['stud_phone', 'stud_address', 'stud_city', 'stud_state', 'stud_zip', 'stud_country']);
        });
    }
};
