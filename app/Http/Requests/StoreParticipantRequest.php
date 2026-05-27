<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// R-37: apostrophes in name fields (O'Brien, D'Angelo) must be accepted.
// Eloquent's parameterized queries handle escaping — no raw SQL concatenation anywhere.
class StoreParticipantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100', 'regex:/^[\pL\s\'\-\.]+$/u'],
            'last_name'  => ['required', 'string', 'max:100', 'regex:/^[\pL\s\'\-\.]+$/u'],
            'email'      => ['required', 'email:rfc,dns', 'max:255'],
            'username'   => ['required', 'string', 'min:4', 'max:50', 'unique:ebc_user_master,user_login_id'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
            'gender'     => ['nullable', 'in:Male,Female,Other,Prefer not to say'],

            // R-31: phone optional at registration; required for SMS recovery later.
            'phone'      => ['nullable', 'string', 'max:50', 'regex:/^[\d\s\+\-\(\)\.]+$/'],

            // Legacy parity (mailing address). State accepts 2-letter for US,
            // free-text for international — R-35 BillingState rule applied via country.
            'address'    => ['nullable', 'string', 'max:200'],
            'city'       => ['nullable', 'string', 'max:100'],
            'state'      => ['nullable', 'string', 'max:100', new \App\Rules\BillingState($this->input('country'))],
            'zip'        => ['nullable', 'string', 'max:20'],
            'country'    => ['nullable', 'string', 'size:2'],   // ISO-3166-1 alpha-2

            // Scholarship / prepaid code (R-33). Validated more deeply in controller.
            'scholarship_code' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.regex' => 'First name may only contain letters, spaces, apostrophes, hyphens, and periods.',
            'last_name.regex'  => 'Last name may only contain letters, spaces, apostrophes, hyphens, and periods.',
            'phone.regex'      => 'Phone may only contain digits, spaces, and the characters + - ( ) .',
            'country.size'     => 'Select a country.',
        ];
    }
}
