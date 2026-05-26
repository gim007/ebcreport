<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $table      = 'ebc_course';
    protected $primaryKey = 'course_id';

    protected $fillable = [
        'inst_id',
        'course_name',
        'course_code',
        'term',
        'schedule_time',
        'course_price',
        'paragraphs',
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'inst_id', 'ins_id');
    }

    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class, 'course_id', 'course_id');
    }
}
