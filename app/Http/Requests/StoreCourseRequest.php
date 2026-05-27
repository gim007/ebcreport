<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_name'   => ['required', 'string', 'max:255'],
            'course_code'   => ['nullable', 'string', 'max:50'],
            'term'          => ['nullable', 'string', 'max:100'],
            'schedule_time' => ['nullable', 'string', 'max:100'],
            'course_price'  => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'expiry_date'   => ['nullable', 'date', 'after_or_equal:today'],
            'is_hidden'     => ['sometimes', 'boolean'],
        ];
    }
}
