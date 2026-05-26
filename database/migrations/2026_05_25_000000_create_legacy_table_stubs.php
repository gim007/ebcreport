<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates minimal stubs of the legacy tables for Docker / fresh-DB development.
 * In production these tables already exist — this migration is a no-op there.
 * Additive migrations (000001–000004) depend on these tables existing first.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ebc_university')) {
            Schema::create('ebc_university', function (Blueprint $table) {
                $table->increments('uni_id');
                $table->string('uni_name');
                $table->string('logo_image')->nullable();
                $table->string('course_price')->nullable();
                $table->text('paragraphs')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('ebc_instructor')) {
            Schema::create('ebc_instructor', function (Blueprint $table) {
                $table->increments('ins_id');
                $table->unsignedInteger('uni_id')->nullable();
                $table->unsignedInteger('user_id')->nullable();
                $table->string('ins_fname');
                $table->string('ins_lname');
                $table->string('ins_email')->nullable();
                $table->string('logo_image')->nullable();
                $table->string('admin_approval')->default('Pending');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('ebc_user_master')) {
            Schema::create('ebc_user_master', function (Blueprint $table) {
                $table->increments('user_id');
                $table->string('user_login_id')->unique();
                $table->string('user_password');
                $table->string('user_email')->nullable();
                $table->enum('user_type', ['stud', 'ins'])->default('stud');
                $table->string('user_status')->default('Active');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('ebc_student')) {
            Schema::create('ebc_student', function (Blueprint $table) {
                $table->increments('stud_id');
                $table->unsignedInteger('user_id')->nullable();
                $table->string('stud_fname');
                $table->string('stud_lname');
                $table->string('stud_email')->nullable();
                $table->string('stud_gender')->nullable();
                $table->integer('tot_credit')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('ebc_course')) {
            Schema::create('ebc_course', function (Blueprint $table) {
                $table->increments('course_id');
                $table->unsignedInteger('inst_id')->nullable();
                $table->string('course_name');
                $table->string('course_code')->nullable();
                $table->string('term')->nullable();
                $table->string('schedule_time')->nullable();
                $table->string('course_price')->nullable();
                $table->text('paragraphs')->nullable();
                $table->string('logo_image')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('ebc_test_result')) {
            Schema::create('ebc_test_result', function (Blueprint $table) {
                $table->increments('test_result_id');
                $table->unsignedInteger('stud_id')->nullable();
                $table->unsignedInteger('course_id')->nullable();
                $table->char('most_result_str', 48)->nullable();
                $table->char('least_result_str', 48)->nullable();
                $table->tinyInteger('focus')->nullable();
                $table->string('disc_most')->nullable();
                $table->string('disc_least')->nullable();
                $table->string('payment_status')->nullable();
                $table->timestamp('result_date')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('ebc_scholarship')) {
            Schema::create('ebc_scholarship', function (Blueprint $table) {
                $table->increments('scholarship_id');
                $table->string('scholarship_code')->unique();
                $table->date('expiration_date')->nullable();
                $table->text('comment')->nullable();
                $table->string('status')->default('active');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('ebc_admin')) {
            Schema::create('ebc_admin', function (Blueprint $table) {
                $table->increments('admin_id');
                $table->string('admin_name');
                $table->string('admin_email')->unique();
                $table->string('admin_password');
                $table->rememberToken();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Only drop if this stub actually created them (i.e., empty tables in dev)
        foreach (['ebc_admin', 'ebc_scholarship', 'ebc_test_result', 'ebc_course',
                  'ebc_student', 'ebc_user_master', 'ebc_instructor', 'ebc_university'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
