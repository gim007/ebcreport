<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Instructor extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logExcept(['updated_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $table      = 'ebc_instructor';
    protected $primaryKey = 'ins_id';

    protected $fillable = [
        'uni_id',
        'user_id',
        'ins_title',
        'ins_fname',
        'ins_lname',
        'ins_gender',
        'ins_email',
        'ins_phone',
        'ins_address',
        'ins_address_cont',
        'ins_city',
        'ins_state',
        'ins_zip',
        'ins_country',
        'ins_timezone',
        'is_hidden',
        'admin_approval',
    ];

    protected $casts = [
        'is_hidden' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'uni_id', 'uni_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'inst_id', 'ins_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->ins_fname} {$this->ins_lname}");
    }

    public function isApproved(): bool
    {
        return $this->admin_approval === 'Approved';
    }
}
