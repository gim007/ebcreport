<?php

namespace App\Auth;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\Hash;

/**
 * Supports legacy MD5 admin passwords during transition.
 * On each successful login the password is silently upgraded to bcrypt.
 */
class AdminHasher implements Hasher
{
    public function info($hashedValue): array
    {
        return Hash::info($hashedValue);
    }

    public function make($value, array $options = []): string
    {
        return Hash::make($value, $options);
    }

    public function check($value, $hashedValue, array $options = []): bool
    {
        if ($this->isMd5($hashedValue)) {
            return md5($value) === $hashedValue;
        }
        return Hash::check($value, $hashedValue, $options);
    }

    public function needsRehash($hashedValue, array $options = []): bool
    {
        // MD5 hashes must be upgraded to bcrypt on next login
        return $this->isMd5($hashedValue) || Hash::needsRehash($hashedValue, $options);
    }

    private function isMd5(string $hash): bool
    {
        return strlen($hash) === 32 && ctype_xdigit($hash);
    }
}
