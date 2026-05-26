<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstructorRequest extends FormRequest
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
            'org_id'     => ['required', 'integer', 'exists:ebc_university,uni_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.regex' => 'First name may only contain letters, spaces, apostrophes, hyphens, and periods.',
            'last_name.regex'  => 'Last name may only contain letters, spaces, apostrophes, hyphens, and periods.',
            'org_id.exists'    => 'Please select a valid organization.',
        ];
    }
}
