<?php

namespace App\Models;

use App\Services\DiscScore;
use App\Services\DiscScoreCalculator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TestResult extends Model
{
    protected $table      = 'ebc_test_result';
    protected $primaryKey = 'test_result_id';

    protected $fillable = [
        'stud_id',
        'course_id',
        'most_result_str',
        'least_result_str',
        'focus',
        'result_date',
        'payment_status',
    ];

    protected $casts = [
        'result_date' => 'datetime',
    ];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'stud_id', 'stud_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    public function emailDelivery(): HasOne
    {
        return $this->hasOne(ReportEmailDelivery::class, 'test_result_id', 'test_result_id');
    }

    public function score(): DiscScore
    {
        return app(DiscScoreCalculator::class)
            ->calculate($this->most_result_str ?? '', $this->least_result_str ?? '');
    }

    public function hasBeenTaken(): bool
    {
        return $this->most_result_str !== null && strlen($this->most_result_str) === 48;
    }
}
