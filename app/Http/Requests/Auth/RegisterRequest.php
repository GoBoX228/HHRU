<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim((string) $this->input('email'))),
            'phone' => trim((string) $this->input('phone')),
            'name' => trim((string) $this->input('name')),
            'company_name' => trim((string) $this->input('company_name')),
            'company_field' => trim((string) $this->input('company_field')),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'role' => ['required', 'in:freelancer,employer'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:50', 'regex:/^[0-9+()\-\s]{7,20}$/', 'unique:users,phone'],
            'company_name' => ['required_if:role,employer', 'nullable', 'string', 'max:255'],
            'company_field' => ['required_if:role,employer', 'nullable', 'string', 'max:255'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers(),
            ],
        ];
    }
}