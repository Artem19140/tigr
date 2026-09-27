<?php

namespace App\Http\Requests\Report;

use App\Http\Dto\MinistryEducationDto;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class MinistryEducationReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'lastWeek' => $this->boolean('lastWeek'),
        ]);
    }

    public function messages(): array
    {
        return [
            'dateTo.before_or_equal' => 'Конец периода не может быть позже сегодняшнего дня.',
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
            'lastWeek' => ['required', 'bool'],
            'dateFrom' => [
                'required_if_declined:lastWeek', 
                'nullable', 
                'date', 
                'before_or_equal:dateTo'
            ],
            'dateTo' => [
                'required_if_declined:lastWeek',
                'nullable', 
                'date',
                Rule::date()->format('Y-m-d'),
                'before_or_equal:today'
            ],
        ];
    }

    public function toDto(): MinistryEducationDto 
    {
        return new MinistryEducationDto(
            Carbon::parse($this->input('dateFrome')),
            Carbon::parse($this->input('dateTo')),
            $this->boolean($this->input('lastWeek'))
        );
    }
    
}