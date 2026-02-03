<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateMarsImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mars_month' => ['required', 'integer', 'between:1,12'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'mars_month.required' => 'Пожалуйста, выберите марсианский месяц',
            'mars_month.integer' => 'Марсианский месяц должен быть числом',
            'mars_month.between' => 'Марсианский месяц должен быть от 1 до 12',
        ];
    }
}
