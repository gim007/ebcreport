<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// R-33: universal subscription codes — one code usable by multiple participants
class Scholarship extends Model
{
    protected $table      = 'ebc_scholarship';
    protected $primaryKey = 'scholarship_id';

    protected $fillable = [
        'scholarship_code',
        'expiration_date',
        'comment',
        'status',
        'max_uses',
        'use_count',
        'expires_at',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'expires_at'      => 'date',
    ];

    public function isValid(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }
        $expiry = $this->expires_at ?? $this->expiration_date;
        if ($expiry && $expiry->isPast()) {
            return false;
        }
        return $this->use_count < $this->max_uses;
    }

    public function redeem(): bool
    {
        if (! $this->isValid()) {
            return false;
        }
        $this->increment('use_count');
        return true;
    }
}
