<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add legacy contact + mailing-address + timezone fields back to ebc_instructor.
 *
 * R-31: phone is required when SMS recovery is used (optional at registration).
 * Other fields match the legacy `instructor_signup.php` form so the new
 * project has feature parity with the existing platform.
 *
 * Email verification: `email_verified_at` on the related User row drives
 * the verified flag; this column on the instructor is intentionally not
 * duplicated. `admin_approval` (already present) stays the gate for full access.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ebc_instructor', function (Blueprint $table) {
            $table->string('ins_title',        50)->nullable()->after('uni_id');
            $table->string('ins_gender',       25)->nullable()->after('ins_lname');
            $table->string('ins_phone',        50)->nullable()->after('ins_email');
            $table->string('ins_address',     200)->nullable()->after('ins_phone');
            $table->string('ins_address_cont',200)->nullable()->after('ins_address');
            $table->string('ins_city',        100)->nullable()->after('ins_address_cont');
            $table->string('ins_state',       100)->nullable()->after('ins_city');   // 2-letter US / free-text intl (R-35)
            $table->string('ins_zip',          20)->nullable()->after('ins_state');
            $table->string('ins_country',       2)->nullable()->after('ins_zip');    // ISO-3166-1 alpha-2
            $table->string('ins_timezone',     50)->nullable()->after('ins_country');
        });
    }

    public function down(): void
    {
        Schema::table('ebc_instructor', function (Blueprint $table) {
            $table->dropColumn([
                'ins_title', 'ins_gender', 'ins_phone',
                'ins_address', 'ins_address_cont', 'ins_city',
                'ins_state', 'ins_zip', 'ins_country', 'ins_timezone',
            ]);
        });
    }
};
