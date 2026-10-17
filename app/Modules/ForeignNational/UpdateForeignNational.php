<?php

namespace App\Modules\ForeignNational;

use App\Http\Dto\ForeignNationalUpdateDto;
use App\Models\ForeignNational;
use App\Support\Audit;
use App\Support\ModelAuditChangesBuilder;

final class UpdateForeignNational
{
    public function __construct(
        protected ForeignNationalBeforeSaveValidator $validator,
        protected Audit $audit,
        protected ModelAuditChangesBuilder $builder
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

        $this->audit->log(
            'update',
            $foreignNational,
            [
                'changes' => $this->builder->build($foreignNational)
            ]
        );

        return $foreignNational;
    }
    
}
