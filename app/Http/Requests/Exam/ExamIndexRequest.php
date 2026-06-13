<?php

namespace App\Http\Requests\Exam;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ExamIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if($this->boolean('cancelled')){
            $this->merge([
                'cancelled' => $this->boolean('cancelled')
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cancelled' => ['nullable', 'boolean'],
            'examTypeId' => ['nullable', 'integer', 'min:1'],
            'dateFrom' => ['nullable', 'date'],
            'dateTo' => ['nullable', 'date'],
            'addressId' => ['nullable', 'integer', 'min:1'],
            'id' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
