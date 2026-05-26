<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ebc_instructor', function (Blueprint $table) {
            $table->boolean('is_hidden')->default(false); // R-38: hide/inactive toggle
        });
    }

    public function down(): void
    {
        Schema::table('ebc_instructor', function (Blueprint $table) {
            $table->dropColumn('is_hidden');
        });
    }
};
