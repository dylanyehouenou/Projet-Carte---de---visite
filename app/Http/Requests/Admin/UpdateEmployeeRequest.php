<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() && $this->user()?->is_active;
    }

    public function rules(): array
    {
        return [
            'first_name'     => ['required', 'string', 'max:100'],
            'last_name'      => ['required', 'string', 'max:100'],
            'job_title'      => ['nullable', 'string', 'max:150'],
            'department'     => ['nullable', 'string', 'max:150'],
            'email'          => ['nullable', 'email', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:30'],
            'postal_address' => ['nullable', 'string', 'max:255'],
            'website'        => ['nullable', 'url', 'max:255'],
            'linkedin_url'   => ['nullable', 'url', 'max:255'],
            'calendly_url'   => ['nullable', 'url', 'max:255'],
            'photo'          => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ];
    }
}
