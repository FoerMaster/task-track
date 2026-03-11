<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user()?->id),
            ],
            'full_name' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'], // Лимит 2 МБ
            'timezone' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Поле "Имя пользователя" обязательно.',
            'name.string' => 'Поле "Имя пользователя" должно быть строкой.',
            'name.max' => 'Поле "Имя пользователя" не должно превышать 255 символов.',
            'email.required' => 'Поле "Email" обязательно.',
            'email.email' => 'Поле "Email" должно быть корректным адресом.',
            'email.max' => 'Поле "Email" не должно превышать 255 символов.',
            'email.unique' => 'Этот Email уже используется.',
            'full_name.string' => 'Поле "ФИО" должно быть строкой.',
            'full_name.max' => 'Поле "ФИО" не должно превышать 255 символов.',
            'avatar.image' => 'Файл должен быть изображением.',
            'avatar.mimes' => 'Изображение должно быть одного из форматов: jpeg, png, jpg, gif, svg, webp.',
            'avatar.max' => 'Размер изображения не должен превышать 2 МБ.',
            'timezone.string' => 'Поле "Часовой пояс" должно быть строкой.',
            'timezone.max' => 'Поле "Часовой пояс" не должно превышать 255 символов.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'Имя пользователя',
            'email' => 'Email',
            'full_name' => 'ФИО',
            'avatar' => 'Аватар',
            'timezone' => 'Часовой пояс',
        ];
    }
}
