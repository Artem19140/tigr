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
            'dateTo.before_or_equal' => 'Конец периода не может быть позже сегодняшнего дня.',
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
            'dateFrom' => ['required', 'date'],
            'dateTo' => [
                'required', 
                'date', 
                Rule::date()->format('Y-m-d'),
                'before_or_equal:today'
            ],
        ];
    }
}
