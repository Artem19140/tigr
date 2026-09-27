<?php

namespace App\Http\Requests\Report;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FlatTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'dateTo.before_or_equal' => 'Дата конца не может быть позже сегодняшнего дня.',
            'dateFrom.before_or_equal' => 'Дата начала не может быть позже даты окончания.',
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
            'dateFrom' => [
                'required', 
                'date',
                'before_or_equal:dateTo'
            ],
            'dateTo' => [
                'required', 
                'date', 
                Rule::date()->format('Y-m-d'),
                'before_or_equal:today'
            ],
        ];
    }
}
