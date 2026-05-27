<?php

namespace App\Http\Requests;

use App\Rules\BillingState;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Instructor registration — matches the legacy ebcdisc.com flow plus SOW R-31
 * (phone) and R-35 (state-format-per-country).
 */
class StoreInstructorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Identity
            'title'      => ['nullable', 'string', 'max:50'],
            'first_name' => ['required', 'string', 'max:100', 'regex:/^[\pL\s\'\-\.]+$/u'],
            'last_name'  => ['required', 'string', 'max:100', 'regex:/^[\pL\s\'\-\.]+$/u'],
            'gender'     => ['nullable', 'in:Male,Female,Other,Prefer not to say'],

            // Account
            'username'   => ['required', 'string', 'min:4', 'max:50', 'unique:ebc_user_master,user_login_id'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],

            // Contact (R-31)
            'phone'      => ['required', 'string', 'max:50', 'regex:/^[\d\s\+\-\(\)\.]+$/'],
            'email'      => ['required', 'email:rfc,dns', 'max:255'],

            // Organization
            'org_id'     => ['required', 'integer', 'exists:ebc_university,uni_id'],

            // Mailing address (legacy parity)
            'address'      => ['required', 'string', 'max:200'],
            'address_cont' => ['nullable', 'string', 'max:200'],
            'city'         => ['required', 'string', 'max:100'],
            'state'        => ['required', 'string', 'max:100', new BillingState($this->input('country'))],
            'zip'          => ['required', 'string', 'max:20'],
            'country'      => ['required', 'string', 'size:2'],   // ISO-3166-1 alpha-2
            'timezone'     => ['required', 'string', 'max:50'],   // IANA tz name (America/Chicago, Europe/London, …)
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.regex' => 'First name may only contain letters, spaces, apostrophes, hyphens, and periods.',
            'last_name.regex'  => 'Last name may only contain letters, spaces, apostrophes, hyphens, and periods.',
            'phone.regex'      => 'Phone may only contain digits, spaces, and the characters + - ( ) .',
            'org_id.exists'    => 'Please select a valid organization.',
        ];
    }
}
