<?php

namespace App\Http\Requests\ForeignNational;

use App\Http\Dto\ForeignNationalUpdateDto;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;

class ForeignNationalUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => preg_replace('/\D/', '', $this->phone),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $countries = Cache::rememberForever('countries_list', function () {
            return collect(json_decode(file_get_contents(storage_path('app/public/countries.json')), true))
                    ->pluck('value')
                    ->toArray();
        });


        return [
            'noPatronymic' => [
                'required',
                'boolean',
            ],
            'noPatronymicLatin' => [
                'required',
                'boolean',
            ],
            'noPassportNumber' => [
                'required',
                'boolean',
            ],
            'noPassportSeries' => [
                'required',
                'boolean',
            ],
            'surname' => [
                'required',
                'string',
            ],
            'name' => [
                'required',
                'string',
            ],
            'patronymic' => [
                'prohibited_if_accepted:noPatronymic',
                'required_if_declined:noPatronymic',
                'nullable',
                'string',
            ],
            'patronymicLatin' => [
                'prohibited_if_accepted:noPatronymicLatin',
                'required_if_declined:noPatronymicLatin',
                'nullable',
                'string',
            ],
            'dateBirth' => [
                'required',
                'date',
            ],
            'surnameLatin' => [
                'required',
                'string',
            ],
            'nameLatin' => [
                'required',
                'string',
            ],
            'passportNumber' => [
                'prohibited_if_accepted:noPassportNumber',
                'required_if_declined:noPassportNumber',
                'nullable',
                'string',
            ],
            'passportSeries' => [
                'prohibited_if_accepted:noPassportSeries',
                'required_if_declined:noPassportSeries',
                'nullable',
                'string',
            ],
            'issuedBy' => [
                'required',
                'string',
            ],
            'issuedDate' => [
                'required',
                'date',
            ],

            'citizenship' => [
                'required',
                'string',
                'max:2',
                'min:2',
                Rule::in($countries),
            ],
            'noPhone' => [
                'required',
                'boolean'  
            ],
            'phone' => [
                'prohibited_if_accepted:noPhone',
                'required_if_declined:noPhone',
                'string',
                'regex:/^\d+$/',  
                'digits:10',   
            ],
            'gender' => [
                'required',
                'string',
                'size:1',
                'in:M,F',
            ],
            'comment' => [
                'nullable',
                'string',
            ],
            'addressReg' => [
                'required',
                'string',
            ]
        ];
    }

    public function toDto(): ForeignNationalUpdateDto
    {
        return new ForeignNationalUpdateDto(
            surname: $this->surname,
            name: $this->name,
            patronymic: $this->patronymic,
            dateBirth:  Carbon::parse($this->dateBirth),

            surnameLatin: $this->surnameLatin,
            nameLatin: $this->nameLatin,
            patronymicLatin: $this->patronymicLatin,

            passportNumber: $this->passportNumber,
            passportSeries: $this->passportSeries,
            issuedBy: $this->issuedBy,
            issuedDate: Carbon::parse($this->issuedDate),

            citizenship: $this->citizenship,
            phone: $this->phone,
            addressReg: $this->addressReg,

            gender: $this->gender,
            comment: $this->comment,
        );
    }
}
