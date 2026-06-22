<?php

namespace App\Modules\ForeignNational;

use App\Http\Dto\ForeignNationalStoreDto;
use App\Modules\Document\DocumentSaver;
use App\Models\Employee;
use App\Models\ForeignNational;


final class StoreForeignNational
{
    public function __construct(
        protected ForeignNationalBeforeSaveValidator $validator,
        protected DocumentSaver $documentSaver
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
            'creator_id' => $employee->id,
            'center_id' => $employee->center_id,
        ]);

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
}
