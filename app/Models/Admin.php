<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable implements FilamentUser, HasName
{
    protected $table      = 'ebc_admin';
    protected $primaryKey = 'admin_id';

    protected $fillable = [
        'admin_name',
        'admin_email',
        'admin_password',
    ];

    protected $hidden = ['admin_password'];

    public function getAuthPassword(): string
    {
        return $this->admin_password;
    }

    public function getAuthPasswordName(): string
    {
        return 'admin_password';
    }

    public function getFilamentName(): string
    {
        return $this->admin_name;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
