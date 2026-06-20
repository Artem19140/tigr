<?php

namespace App\Http\Dto;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

final readonly class ForeignNationalStoreDto
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
        public UploadedFile $passport,
        public UploadedFile $passportTranslate,
    ) {}
}
