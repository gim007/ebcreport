<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ebc_student', function (Blueprint $table) {
            if (! Schema::hasColumn('ebc_student', 'inst_id')) {
                $table->unsignedInteger('inst_id')->nullable();
            }
            if (! Schema::hasColumn('ebc_student', 'course_id')) {
                $table->unsignedInteger('course_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('ebc_student', function (Blueprint $table) {
            $table->dropColumnIfExists('inst_id');
            $table->dropColumnIfExists('course_id');
        });
    }
};
