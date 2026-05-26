<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * R-35: state field accepts the standard two-letter abbreviation for US
 * billing addresses (IL, TX, CA, …); free-text for international addresses.
 *
 * The legacy system converted the state value to a numeric ID before
 * forwarding to the payment processor, breaking international billing
 * and corrupting US billing. The new rule enforces correct shape on the
 * way in so the value reaches Stripe (or any future processor) unchanged.
 */
class BillingState implements ValidationRule
{
    public function __construct(private readonly ?string $country = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || trim($value) === '') {
            return; // empty = leave to required/nullable
        }

        // US: must be exactly two uppercase letters (one of 50 + DC + territories)
        if (strtoupper((string) $this->country) === 'US') {
            if (! preg_match('/^[A-Z]{2}$/', $value)) {
                $fail('The :attribute must be a two-letter US state abbreviation (e.g. IL, TX, CA).');
            }
            return;
        }

        // Non-US: free-text province/region; reasonable length bound
        if (mb_strlen($value) > 100) {
            $fail('The :attribute may not be longer than 100 characters.');
        }
    }
}
