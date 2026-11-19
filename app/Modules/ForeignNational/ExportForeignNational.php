<?php

namespace App\Modules\ForeignNational;

use App\Models\ForeignNational;
use App\Support\CsvWriter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ExportForeignNational
{
    public function __construct(
        protected CsvWriter $csvWriter
    ){}
    public function execute(
        Carbon $dateFrom,
        Carbon $dateTo,
        ?string $citizenship
    ) {
        $this->csvWriter->setHeaders($this->headers());
        
        $count = 0;
        ForeignNational::query()
            ->select(['id', 'surname', 'name', 'patronymic', 'citizenship', 'passport_series', 'passport_number'])
            ->when($citizenship, function (Builder $query) use ($citizenship) {
                $query->where('citizenship', $citizenship);
            })

            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('id')
            ->lazyById(1000)
            ->each(function ($i) use (&$count) {
                $this->csvWriter->writeRow([
                    $i->surname,
                    $i->name,
                    $i->patronymic,
                    $i->citizenship,
                    $i->passport_series,
                    $i->passport_number,
                ]);
                $count++;
            });  
    }

    protected function headers(): array
    {
        return [
            'Фамилия',
            'Имя',
            'Отчество',
            'Гражданство',
            'Серия паспорта',
            'Номер паспорта',
        ];
    }
}
