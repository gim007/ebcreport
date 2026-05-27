<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// UI label: "Participant" (R-36). DB table: ebc_student (unchanged).
class Participant extends Model
{
    protected $table      = 'ebc_student';
    protected $primaryKey = 'stud_id';

    protected $fillable = [
        'user_id',
        'stud_fname',
        'stud_lname',
        'stud_email',
        'stud_gender',
        'stud_phone',
        'stud_address',
        'stud_city',
        'stud_state',
        'stud_zip',
        'stud_country',
        'tot_credit',
        'inst_id',
        'course_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class, 'stud_id', 'stud_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'inst_id', 'ins_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->stud_fname} {$this->stud_lname}");
    }
}
