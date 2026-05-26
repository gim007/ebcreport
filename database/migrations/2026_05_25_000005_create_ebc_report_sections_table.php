<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // R-14, R-17: modular section registry — each section is toggleable per org
    public function up(): void
    {
        Schema::create('ebc_report_sections', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // S-01 … S-32
            $table->string('name');
            $table->unsignedSmallInteger('sort_order');
            $table->boolean('is_active')->default(true); // S-28 to S-32 start as false
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebc_report_sections');
    }
};
