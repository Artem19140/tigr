<?php

namespace App\Http\Requests\ForeignNational;

use App\Http\Dto\ForeignNationalStoreDto;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class ForeignNationalPostRequest extends FormRequest
{
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

    public function messages(): array
    {
        return [
            'issuedDate.before_or_equal' => 'Дата выдачи не может быть позже сегодняшнего дня.',
            'dateBirth.before_or_equal' => 'Дата рождения не может быть позже сегодняшнего дня.',
            'surnameLatin.regex' => 'Фамилия на латинице должна содержать только латинские буквы. Допускаются пробел, дефис и апостроф.',
            'nameLatin.regex' => 'Имя на латинице должно содержать только латинские буквы. Допускаются пробел, дефис и апостроф.',
            'patronymicLatin.regex' => 'Отчество на латинице должно содержать только латинские буквы. Допускаются пробел, дефис и апостроф.',
            'surname.regex' => 'Фамилия должна содержать только кириллические буквы. Допускаются пробел, дефис и апостроф.',
            'name.regex' => 'Имя должно содержать только кириллические буквы. Допускаются пробел, дефис и апостроф.',
            'patronymic.regex' => 'Отчество должно содержать только кириллические буквы. Допускаются пробел, дефис и апостроф.',
        ];  
    }

    public function rules(): array
    {
        $countries = collect(json_decode(file_get_contents(storage_path('app/public/countries.json')), true))
            ->pluck('value')
            ->toArray();

        return [
            'hasPayment' => [
                'required',
                'boolean',
            ],
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
                'regex:/^[А-Яа-яЁё]+(?:[ -\'][А-Яа-яЁё]+)*$/u'
            ],
            'name' => [
                'required',
                'string',
                'regex:/^[А-Яа-яЁё]+(?:[ -\'][А-Яа-яЁё]+)*$/u'
            ],
            'patronymic' => [
                'prohibited_if_accepted:noPatronymic',
                'required_if_declined:noPatronymic',
                'nullable',
                'string',
                'regex:/^[А-Яа-яЁё]+(?:[ -\'][А-Яа-яЁё]+)*$/u'
            ],
            'patronymicLatin' => [
                'prohibited_if_accepted:noPatronymicLatin',
                'required_if_declined:noPatronymicLatin',
                'nullable',
                'string',
                'regex:/^[A-Za-z]+(?:[ -\'][A-Za-z]+)*$/'
            ],
            'dateBirth' => [
                'required',
                'date',
                Rule::date()->format('Y-m-d'),
                'before_or_equal:today'
            ],
            'surnameLatin' => [
                'required',
                'string',
                'regex:/^[A-Za-z]+(?:[ -\'][A-Za-z]+)*$/'
            ],
            'nameLatin' => [
                'required',
                'string',
                'regex:/^[A-Za-z]+(?:[ -\'][A-Za-z]+)*$/'
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
                'string'
            ],
            'issuedDate' => [
                'required',
                'date',
                Rule::date()->format('Y-m-d'),
                'before_or_equal:today'
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
            'comment' => [
                'nullable',
                'string',
            ],
            'examId' => [
                'required',
                'integer',
                'min:1',
                'exists:exams,id',
            ],
            'gender' => [
                'required',
                'string',
                'size:1',
                'in:M,F',
            ],
            'addressReg' => [
                'required',
                'string',
            ],
            'passportTranslate' => [
                'required',
                'mimes:pdf',
                File::types(['pdf'])
                    ->max('20mb'),
            ],
            'passport' => [
                'required',
                'mimes:pdf',
                File::types(['pdf'])
                    ->max('20mb'),
            ],
        ];
    }

    public function toDto(): ForeignNationalStoreDto
    {
        return new ForeignNationalStoreDto(
            surname: $this->surname,
            name: $this->name,
            patronymic: $this->patronymic,
            dateBirth: Carbon::parse($this->dateBirth),

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

            passport: $this->file('passport'),
            passportTranslate: $this->file('passportTranslate'),
        );
    }
}
