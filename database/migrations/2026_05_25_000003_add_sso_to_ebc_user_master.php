<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ebc_user_master', function (Blueprint $table) {
            $table->string('social_provider')->nullable(); // R-30: google / facebook / apple
            $table->string('social_id')->nullable();
            $table->string('phone')->nullable();           // R-31: SMS OTP verification

            $table->index(['social_provider', 'social_id'], 'idx_social_login');
        });
    }

    public function down(): void
    {
        Schema::table('ebc_user_master', function (Blueprint $table) {
            $table->dropIndex('idx_social_login');
            $table->dropColumn(['social_provider', 'social_id', 'phone']);
        });
    }
};
