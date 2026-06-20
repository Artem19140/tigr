<?php

namespace App\Modules\ForeignNational;

use App\Http\Dto\ForeignNationalStoreDto;
use App\Modules\Document\DocumentSaver;
use App\Modules\ForeignNational\ForeignNationalGuard;
use App\Models\Employee;
use App\Models\ForeignNational;


final class StoreForeignNational
{
    public function __construct(
        protected ForeignNationalGuard $foreignNationalGuard,
        protected DocumentSaver $documentSaver
    ) {}

    public function execute(
        ForeignNationalStoreDto $dto,
        Employee $employee,
        
    ): ForeignNational {
        $this->foreignNationalGuard->ensureAge($dto->dateBirth);
        $this->foreignNationalGuard->ensureUniquePassport(
            $dto->passportSeries,
            $dto->passportNumber
        );
        
        $foreignNational = ForeignNational::create(
            $this->attributes($dto, $employee),
        );

        $this->documentSaver->store(
            $dto->passportTranslate,
            $foreignNational,
            'passport_translate'
        );

        $this->documentSaver->store(
            $dto->passport,
            $foreignNational,
            'passport'
        );
        return $foreignNational;
    }

    private function attributes(
        ForeignNationalStoreDto $dto,
        Employee $creator,
    ): array {
        return [
            'surname' => $dto->surname,
            'name' => $dto->name,
            'patronymic' => $dto->patronymic,
            'date_birth' => $dto->dateBirth,
            'surname_latin' => $dto->surnameLatin,
            'name_latin' => $dto->nameLatin,
            'patronymic_latin' => $dto->patronymicLatin,
            'passport_number' => $dto->passportNumber,
            'passport_series' => $dto->passportSeries,
            'issued_by' => $dto->issuedBy,
            'issued_date' =>  $dto->issuedDate,
            'citizenship' =>  $dto->citizenship,
            'phone' =>  $dto->phone,
            'address_reg' => $dto->addressReg,
            'creator_id' => $creator->id,
            'center_id' => $creator->center_id,
            'gender' => $dto->gender,
            'comment' => $dto->comment,
            'surname_normalized' => $this->normalize($dto->surname),
            'name_normalized' => $this->normalize($dto->name),
            'patronymic_normalized' => $this->normalize($dto->patronymic),
            'passport_number_normalized' => $this->normalize($dto->passportNumber),
            'passport_series_normalized' => $this->normalize($dto->passportSeries),
        ];
    }

    protected function normalize(?string $value): string
    {
        if (! $value) {
            return '';
        }
        $value = trim($value);

        if (class_exists(\Normalizer::class)) {
            $value = \Normalizer::normalize($value, \Normalizer::FORM_C);
        }

        $value = mb_strtolower($value, 'UTF-8');

        return $value;
    }
}
