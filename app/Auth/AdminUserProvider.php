<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;

// Remaps the generic 'email' credential key to the legacy 'admin_email' column
// so Filament's standard login form works without DB schema changes.
class AdminUserProvider extends EloquentUserProvider
{
    public function retrieveByCredentials(array $credentials): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        if (array_key_exists('email', $credentials)) {
            $credentials['admin_email'] = $credentials['email'];
            unset($credentials['email']);
        }

        return parent::retrieveByCredentials($credentials);
    }
}
