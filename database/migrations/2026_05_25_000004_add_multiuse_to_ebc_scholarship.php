<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // R-33: universal subscription codes — single code usable by many participants
    public function up(): void
    {
        Schema::table('ebc_scholarship', function (Blueprint $table) {
            $table->unsignedInteger('max_uses')->default(1);
            $table->unsignedInteger('use_count')->default(0);
            $table->date('expires_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ebc_scholarship', function (Blueprint $table) {
            $table->dropColumn(['max_uses', 'use_count', 'expires_at']);
        });
    }
};
