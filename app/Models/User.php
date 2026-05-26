<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table      = 'ebc_user_master';
    protected $primaryKey = 'user_id';

    // Legacy auth uses user_login_id as the "username" credential
    protected $fillable = [
        'user_login_id',
        'user_password',
        'user_email',
        'user_type',
        'user_status',
        'social_provider',
        'social_id',
        'phone',
    ];

    protected $hidden = ['user_password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'user_password' => 'hashed',
        ];
    }

    // Override the default auth password field name
    public function getAuthPassword(): string
    {
        return $this->user_password;
    }

    public function participant(): HasOne
    {
        return $this->hasOne(Participant::class, 'user_id', 'user_id');
    }

    public function instructor(): HasOne
    {
        return $this->hasOne(Instructor::class, 'user_id', 'user_id');
    }

    public function isInstructor(): bool
    {
        return $this->user_type === 'ins';
    }

    public function isParticipant(): bool
    {
        return $this->user_type === 'stud';
    }
}
