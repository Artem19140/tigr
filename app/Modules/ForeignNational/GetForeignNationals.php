<?php

namespace App\Modules\ForeignNational;

use App\Http\Dto\ForeignNationalIndexDto;
use App\Models\ForeignNational;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;

class GetForeignNationals
{
    public function execute(ForeignNationalIndexDto $dto): Paginator
    {
        $surname = $dto->surname ?? false;
        $name = $dto->name  ?? false;
        $patronymic = $dto->patronymic ?? false;
        $passportSeries = $dto->passportSeries  ?? false;
        $passportNumber = $dto->passportNumber  ?? false;
        $id = $dto->id  ?? false;
        $perPage =$dto->perPage  ?? 10;

        return ForeignNational::query()
            ->when($surname, function (Builder $query) use ($surname) {
                $surname = mb_strtolower(trim($surname), 'UTF-8');
                $query->where('surname_normalized', 'LIKE', $surname.'%');
            })
            ->when($name, function (Builder $query) use ($name) {
                $name = mb_strtolower(trim($name), 'UTF-8');
                $query->where('name_normalized', 'ILIKE', $name.'%');
            })
            ->when($patronymic, function (Builder $query) use ($patronymic) {
                $patronymic = mb_strtolower(trim($patronymic), 'UTF-8');
                $query->where('patronymic_normalized', 'like', $patronymic.'%');
            })
            ->when($passportSeries, function (Builder $query) use ($passportSeries) {
                $passportSeries = mb_strtolower(trim($passportSeries), 'UTF-8');
                $query->where('passport_series_normalized', $passportSeries);
            })
            ->when($passportNumber, function (Builder $query) use ($passportNumber) {
                $passportNumber = mb_strtolower(trim($passportNumber), 'UTF-8');
                $query->where('passport_number_normalized', $passportNumber);
            })
            ->when($id, function (Builder $query) use ($id) {
                $query->where('id', trim($id));
            })
            ->latest('id')
            ->simplePaginate($perPage)
            ->withQueryString();
    }
}
