<?php

namespace App\Http\Dto;

use Carbon\Carbon;

final readonly class ExamIndexDto
{
    public function __construct(
        public int | null $id,
        public int | null $addressId,
        public int | null $examTypeId,
        public Carbon | null $dateFrom,
        public Carbon | null $dateTo,
        public bool | null $cancelled
    ){}

    public function toFilters()
    {
        return [
            'id' => $this->id,
            'addressId' =>   $this->addressId,
            'examTypeId' =>   $this->examTypeId,
            'dateFrom' =>  $this->dateFrom?->format('Y-m-d'),
            'dateTo' =>  $this->dateTo?->format('Y-m-d'),
            'cancelled' =>  $this->cancelled
        ];
    }
}