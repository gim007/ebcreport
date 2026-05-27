<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the Laravel-standard `email_verified_at` column to ebc_user_master so
 * instructors (and any other future user types) can use Laravel's built-in
 * MustVerifyEmail signed-URL verification flow.
 *
 * Legacy parity: ebcdisc.com's instructor_signup.php sent a verification
 * email and instructor_verification.php confirmed the click. We do the same
 * with the framework's signed routes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ebc_user_master', function (Blueprint $table) {
            $table->timestamp('email_verified_at')->nullable()->after('user_email');
        });
    }

    public function down(): void
    {
        Schema::table('ebc_user_master', function (Blueprint $table) {
            $table->dropColumn('email_verified_at');
        });
    }
};
