<?php

namespace App\Modules\ForeignNational;

use App\Http\Dto\ForeignNationalUpdateDto;
use App\Modules\ForeignNational\ForeignNationalGuard;
use App\Models\ForeignNational;
use App\Support\ModelChangesLogger;

final class UpdateForeignNational
{
    public function __construct(
        protected ForeignNationalGuard $foreignNationalGuard,
        protected ModelChangesLogger $logger
    ) {}

    public function execute(
        ForeignNationalUpdateDto $dto,
        ForeignNational $foreignNational,
    ): ForeignNational {
        $this->foreignNationalGuard->ensureAge($dto->dateBirth);

        $this->foreignNationalGuard->ensureUniquePassport(
            $dto->passportSeries,
            $dto->passportNumber, 
            $foreignNational->id
        );

        $foreignNational->update(
            $this->attributes($dto)
        );

        $foreignNational->save();
        $this->logger->log($foreignNational);

        return $foreignNational;
    }

    protected function attributes(ForeignNationalUpdateDto $dto): array
    {
        return [
            'surname' => $dto->surname,
            'name' => $dto->name,
            'patronymic' => $dto->patronymic,
            'date_birth' => $dto->dateBirth,
            'surname_latin' => $dto->surnameLatin,
            'name_latin' => $dto->nameLatin,
            'patronymic_latin' =>$dto->patronymicLatin,
            'passport_number' => $dto->passportNumber,
            'passport_series' => $dto->passportSeries,
            'issued_by' => $dto->issuedBy,
            'issued_date' => $dto->issuedDate,
            'citizenship' => $dto->citizenship,
            'phone' => $dto->phone,
            'gender' => $dto->gender,
            'address_reg' => $dto->addressReg,
            'comment' => $dto->comment ?? '',
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
