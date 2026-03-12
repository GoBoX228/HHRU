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
            'email' => ['required', 'email:filter', 'max:255', 'unique:users,email'],
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

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'role.required' => 'Выберите тип аккаунта.',
            'role.in' => 'Некорректный тип аккаунта.',

            'name.required' => 'Введите ФИО.',
            'name.string' => 'Поле "ФИО" должно быть строкой.',
            'name.max' => 'ФИО не должно превышать :max символов.',

            'email.required' => 'Введите email.',
            'email.email' => 'Введите корректный email.',
            'email.max' => 'Email не должен превышать :max символов.',
            'email.unique' => 'Пользователь с таким email уже существует.',

            'phone.required' => 'Введите номер телефона.',
            'phone.string' => 'Поле "номер телефона" должно быть строкой.',
            'phone.max' => 'Номер телефона не должен превышать :max символов.',
            'phone.regex' => 'Введите корректный номер телефона.',
            'phone.unique' => 'Пользователь с таким номером телефона уже существует.',

            'company_name.required_if' => 'Введите название компании.',
            'company_name.string' => 'Поле "название компании" должно быть строкой.',
            'company_name.max' => 'Название компании не должно превышать :max символов.',

            'company_field.required_if' => 'Введите направление компании.',
            'company_field.string' => 'Поле "направление компании" должно быть строкой.',
            'company_field.max' => 'Направление компании не должно превышать :max символов.',

            'password.required' => 'Введите пароль.',
            'password.string' => 'Поле "пароль" должно быть строкой.',
            'password.confirmed' => 'Пароли не совпадают.',
            'password.min' => 'Пароль должен содержать минимум :min символов.',
            'password.letters' => 'Пароль должен содержать хотя бы одну букву.',
            'password.mixed' => 'Пароль должен содержать буквы в разных регистрах.',
            'password.mixed_case' => 'Пароль должен содержать буквы в разных регистрах.',
            'password.numbers' => 'Пароль должен содержать хотя бы одну цифру.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'role' => 'тип аккаунта',
            'name' => 'ФИО',
            'email' => 'email',
            'phone' => 'номер телефона',
            'company_name' => 'название компании',
            'company_field' => 'направление компании',
            'password' => 'пароль',
            'password_confirmation' => 'подтверждение пароля',
        ];
    }
}
