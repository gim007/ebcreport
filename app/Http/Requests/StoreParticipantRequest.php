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
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.regex' => 'First name may only contain letters, spaces, apostrophes, hyphens, and periods.',
            'last_name.regex'  => 'Last name may only contain letters, spaces, apostrophes, hyphens, and periods.',
        ];
    }
}
