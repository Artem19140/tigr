<?php

namespace App\Modules\ForeignNational\Guard;

use App\Models\ForeignNational;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class ForeignNationalGuard
{
    public function ensureAge(string $dateBirth): void
    {
        $age = Carbon::parse($dateBirth)->age;

        if ($age < 18) {
            throw ValidationException::withMessages([
                'dateBirth' => 'На экзамен можно записывать с 18 лет',
            ]);
        }
    }

    public function ensureUniquePassport(
        array $data,
        ?int $ignoreId = null
    ): void {
        $uniquePassportData = ForeignNational::where('passport_number', $data['passportNumber'])
            ->where('passport_series', $data['passportSeries'])
            ->when($ignoreId, function (Builder $query) use ($ignoreId) {
                $query->where('id', '<>', $ignoreId);
            })
            ->where('id', '<>', $ignoreId)
            ->where('citizenship', $data['citizenship'])
            ->exists();

        if ($uniquePassportData) {
            throw ValidationException::withMessages([
                'passportSeries' => 'ИГ с такими паспортными данными уже существует',
                'passportNumber' => 'ИГ с такими паспортными данными уже существует',
            ]);
        }
    }
}
