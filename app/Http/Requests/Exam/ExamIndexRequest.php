<?php

namespace App\Http\Requests\Exam;

use App\Http\Dto\ExamIndexDto;
use App\Modules\Shared\CenterData;
use Carbon\Carbon;
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

    public function toDto(): ExamIndexDto
    {   
        return new ExamIndexDto(
            id: $this->id,
            addressId: $this->addressId,
            examTypeId: $this->examTypeId,
            dateFrom: $this->dateFrom ? Carbon::parse($this->dateFrom)->setTimezone(CenterData::timeZome()) : null,
            dateTo: $this->dateFrom ? Carbon::parse($this->dateTo)->setTimezone(CenterData::timeZome()) : null,
            cancelled: $this->cancelled
        );
    }
}
