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

        $foreignNational->update($dto->toArray());

        $foreignNational->save();
        $this->logger->log($foreignNational);

        return $foreignNational;
    }
}
