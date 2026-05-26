<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // R-25: track auto PDF email delivery status; support manual resend from admin
    public function up(): void
    {
        Schema::create('ebc_report_email_deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('test_result_id');
            $table->string('recipient_email');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('test_result_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebc_report_email_deliveries');
    }
};
