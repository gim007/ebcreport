<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // R-15: per-organization section enable/disable overrides
    public function up(): void
    {
        Schema::create('ebc_org_section_config', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('org_id');
            $table->string('section_code', 10);
            $table->boolean('enabled')->default(true);
            $table->unique(['org_id', 'section_code']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebc_org_section_config');
    }
};
