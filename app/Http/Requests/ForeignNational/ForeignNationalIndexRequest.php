<?php

namespace App\Http\Requests\ForeignNational;

use App\Http\Dto\ForeignNationalIndexDto;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ForeignNationalIndexRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['nullable', 'integer', 'min:1'],
            'surname' => ['nullable', 'string'],
            'name' => ['nullable', 'string'],
            'patronymic' => ['nullable', 'string'],
            'passportSeries' => ['nullable', 'string'],
            'passportNumber' => ['nullable', 'string'],
            'perPage' => ['nullable', 'integer', 'max:100', 'min:1'],
        ];
    }

    public function toDto(): ForeignNationalIndexDto
    {
        return new ForeignNationalIndexDto(
            id: $this->id,
            surname: $this->surname,
            name: $this->name,
            patronymic: $this->patronymic,
            passportSeries:$this->passportSeries,
            passportNumber: $this->passportNumber,
            perPage: $this->perPage
        );
    }
}
