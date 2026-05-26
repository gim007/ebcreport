<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = ['focus' => ['required', 'integer', 'in:1,2,3']];

        for ($i = 1; $i <= 48; $i++) {
            $rules["M{$i}"] = ['required', 'string', 'size:1', 'regex:/^[1-9]$/'];
            $rules["L{$i}"] = ['required', 'string', 'size:1', 'regex:/^[1-9]$/'];
        }

        return $rules;
    }
}
