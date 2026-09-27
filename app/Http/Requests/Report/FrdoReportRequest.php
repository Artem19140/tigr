<?php

namespace App\Http\Requests\Report;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FrdoReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'date.before_or_equal' => 'Дата не может быть позже сегодняшнего дня.',
            'type.in' => 'Парметр должен быть справки или сертификаты'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:certificates,references',],
            'date' => [
                'required', 
                'date',
                Rule::date()->format('Y-m-d'),
                'before_or_equal:today'
            ],
        ];
    }
}
