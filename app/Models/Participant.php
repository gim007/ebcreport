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

    public function getFullNameAttribute(): string
    {
        return trim("{$this->stud_fname} {$this->stud_lname}");
    }
}
