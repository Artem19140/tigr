<?php

namespace App\Modules\ForeignNational;

use App\Http\Dto\ForeignNationalStoreDto;
use App\Models\Employee;
use App\Models\ForeignNational;


final class StoreForeignNational
{
    public function __construct(
        protected ForeignNationalBeforeSaveValidator $validator
    ) {}

    public function execute(
        ForeignNationalStoreDto $dto,
        Employee $employee,
    ): ForeignNational {
        
        $this->validator->validate(
            $dto->dateBirth,
            $dto->passportSeries,
            $dto->passportNumber
        );
        
        $foreignNational = ForeignNational::create([
            ...$dto->toArray(),
            'creator_id' => $employee->id
        ]);

        $passportTranslate = $dto->passportTranslate;

        $passport = $dto->passport;

        $foreignNational->documents()->createMany(
            [
                [
                    'path' => $passportTranslate->store('documents'),
                    'mime_type' =>  $passportTranslate->getMimeType(),
                    'size_bytes' => $passportTranslate->getSize(),
                    'document_type' => 'passport_translate',
                    'creator_id' => auth()->user()->id,
                    'original_name' => $passportTranslate->getClientOriginalName()
                ],
                [
                    'path' => $passport->store('documents'),
                    'mime_type' =>  $passport->getMimeType(),
                    'size_bytes' => $passport->getSize(),
                    'document_type' => 'passport',
                    'creator_id' => auth()->user()->id,
                    'original_name' => $passport->getClientOriginalName()
                ]
            ]
        );
        return $foreignNational;
    }
}
