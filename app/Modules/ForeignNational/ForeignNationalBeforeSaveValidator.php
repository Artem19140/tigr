<?php

namespace App\Modules\ForeignNational;

use App\Models\ForeignNational;
use App\Modules\Shared\ExamSettings;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class ForeignNationalBeforeSaveValidator
{
    public function validate(
        Carbon $dateBirth,
        ?string $passportSeries,
        ?string $passportNumber,
        ?int $ignoreId = null
    ): void
    {
        $this->ensureAge($dateBirth);
        $this->ensureUniquePassport(
            $passportSeries,
            $passportNumber,
            $ignoreId
        );
    }
    public function ensureAge(Carbon $dateBirth): void
    {
        $age = $dateBirth->age;
        $minAgeYear = ExamSettings::minAgeYear();
        if ($age < $minAgeYear) {
            throw ValidationException::withMessages([
                'dateBirth' => "На экзамен возможно записать с $minAgeYear лет",
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
