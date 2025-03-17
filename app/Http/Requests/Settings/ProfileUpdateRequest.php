<?php

namespace App\Http\Requests\Settings;

use App\Models\User;
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
            'full_name' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp' , 'max:2048'], // Лимит 2 МБ
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
            'full_name' => 'ФИО',
            'avatar' => 'Аватар',
            'timezone' => 'Часовой пояс',
        ];
    }
}
