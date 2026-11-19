<?php

namespace App\Modules\ForeignNational;

use App\Http\Dto\ForeignNationalUpdateDto;
use App\Models\ForeignNational;

final class UpdateForeignNational
{
    public function __construct(
        protected ForeignNationalBeforeSaveValidator $validator
    ) {}

    public function execute(
        ForeignNationalUpdateDto $dto,
        ForeignNational $foreignNational,
    ): ForeignNational {

        $this->validator->validate(
            $dto->dateBirth,
            $dto->passportSeries,
            $dto->passportNumber, 
            $foreignNational->id
        );

        $foreignNational->update(
            $dto->toArray()
        );

        return $foreignNational;
    }
    
}
