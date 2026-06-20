<?php

namespace App\Http\Dto;

final readonly class ForeignNationalIndexDto
{
    public function __construct(
        public int | null $id,
        public string | null $surname,
        public string | null $name,
        public string | null $patronymic,
        public string | null $passportSeries,
        public string | null $passportNumber,
        public int | null $perPage,
    ){}

    public function toFilters()
    {
        return [
            'id' => $this->id,
            'surname' =>   $this->surname,
            'name' =>   $this->name,
            'patronymic' =>  $this->patronymic,
            'passportSeries' =>  $this->passportSeries,
            'passportNumber' =>  $this->passportNumber,
            'perPage' =>  $this->perPage
        ];
    }
}