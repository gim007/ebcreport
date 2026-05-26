<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// R-25: tracks auto PDF email delivery status for each completed test result
class ReportEmailDelivery extends Model
{
    protected $table = 'ebc_report_email_deliveries';

    protected $fillable = [
        'test_result_id',
        'recipient_email',
        'status',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function testResult(): BelongsTo
    {
        return $this->belongsTo(TestResult::class, 'test_result_id', 'test_result_id');
    }

    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function wasSent(): bool
    {
        return $this->status === 'sent';
    }
}
