<?php

namespace App\Modules\ForeignNational\Guard;

use App\Models\ForeignNational;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class ForeignNationalGuard
{
    public function ensureAge(Carbon $dateBirth): void
    {
        $age = $dateBirth->age;

        if ($age < 18) {
            throw ValidationException::withMessages([
                'dateBirth' => 'На экзамен можно записывать с 18 лет',
            ]);
        }
    }

    public function ensureUniquePassport(
        ?string $passportSeries,
        ?string $passportNumber,
        ?int $ignoreId = null
    ): void {
        $notUniquePassportData = ForeignNational::query()
            ->when($passportSeries, function(Builder $query) use( $passportSeries ){
                $query->where('passport_series', $passportSeries);
            })
            ->when($passportSeries, function(Builder $query) use( $passportNumber ){
                $query->where('passport_number', $passportNumber);
            })
            ->when($ignoreId, function (Builder $query) use ($ignoreId) {
                $query->where('id', '<>', $ignoreId);
            })
            ->where('id', '<>', $ignoreId)
            ->exists();

        if ($notUniquePassportData) {
            throw ValidationException::withMessages([
                'passportSeries' => 'ИГ с такими паспортными данными уже существует',
                'passportNumber' => 'ИГ с такими паспортными данными уже существует',
            ]);
        }
    }
}
