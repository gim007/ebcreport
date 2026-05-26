<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ebc_university', function (Blueprint $table) {
            $table->string('logo_path')->nullable();      // R-18: org logo (spatie/medialibrary path)
            $table->boolean('is_hidden')->default(false); // R-38: hide/inactive toggle
        });
    }

    public function down(): void
    {
        Schema::table('ebc_university', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'is_hidden']);
        });
    }
};
