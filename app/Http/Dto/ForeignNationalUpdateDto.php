<?php

namespace App\Http\Dto;

use Carbon\Carbon;

final readonly class ForeignNationalUpdateDto
{
    public function __construct(
        public string $surname,
        public string $name,
        public ?string $patronymic,
        public Carbon $dateBirth,
        public string $surnameLatin,
        public string $nameLatin,
        public ?string $patronymicLatin,
        public ?string $passportNumber,
        public ?string $passportSeries,
        public string $issuedBy,
        public Carbon $issuedDate,
        public string $citizenship,
        public ?string $phone,
        public string $addressReg,
        public string $gender,
        public ?string $comment,
    ) {}

    public function toArray(): array
    {
        return [
            'surname' => $this->surname,
            'name' => $this->name,
            'patronymic' => $this->patronymic,
            'date_birth' => $this->dateBirth,
            'surname_latin' => $this->surnameLatin,
            'name_latin' => $this->nameLatin,
            'patronymic_latin' => $this->patronymicLatin,
            'passport_number' => $this->passportNumber,
            'passport_series' => $this->passportSeries,
            'issued_by' => $this->issuedBy,
            'issued_date' => $this->issuedDate,
            'citizenship' => $this->citizenship,
            'phone' => $this->phone,
            'address_reg' => $this->addressReg,
            'gender' => $this->gender,
            'comment' => $this->comment,
            'surname_normalized' => $this->normalize($this->surname),
            'name_normalized' => $this->normalize($this->name),
            'patronymic_normalized' => $this->normalize($this->patronymic),
            'passport_number_normalized' => $this->normalize($this->passportNumber),
            'passport_series_normalized' => $this->normalize($this->passportSeries),
        ];
    }

    protected function normalize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim($value);

        if (class_exists(\Normalizer::class)) {
            $value = \Normalizer::normalize($value, \Normalizer::FORM_C);
        }

        $value = mb_strtolower($value, 'UTF-8');

        return $value;
    }
}
