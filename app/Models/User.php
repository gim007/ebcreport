<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail, CanResetPassword
{
    use Notifiable, CanResetPasswordTrait;

    protected $table      = 'ebc_user_master';
    protected $primaryKey = 'user_id';

    // Legacy auth uses user_login_id as the "username" credential
    protected $fillable = [
        'user_login_id',
        'user_password',
        'user_email',
        'email_verified_at',
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
            'user_password'     => 'hashed',
            'email_verified_at' => 'datetime',
        ];
    }

    public function getAuthPassword(): string
    {
        return $this->user_password;
    }

    /** Laravel's MustVerifyEmail uses `email` by default; map to legacy column. */
    public function getEmailForVerification(): string
    {
        return $this->user_email;
    }

    /** CanResetPassword: same mapping for the password-reset broker. */
    public function getEmailForPasswordReset(): string
    {
        return $this->user_email;
    }

    /** Laravel emails verification + password-reset notifications via this address. */
    public function routeNotificationForMail(): string
    {
        return $this->user_email;
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
