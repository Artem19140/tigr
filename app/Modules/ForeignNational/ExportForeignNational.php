<?php

namespace App\Modules\ForeignNational;

use App\Modules\Center\CenterContext;
use App\Models\ForeignNational;
use App\Support\CenterIsolationCheck;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class ExportForeignNational
{
    public function __construct(
        protected CenterIsolationCheck $centerIsolationCheck,
        protected CenterContext $centerContext
    ){}
    public function execute(
        Carbon $dateFrom,
        Carbon $dateTo,
        ?string $citizenship
    ) {
        $handle = fopen('php://output', 'w');
        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, $this->headers());
        $count = 0;
        ForeignNational::query()
            ->forCenter($this->centerContext->id())
            ->select(['id', 'surname', 'name', 'patronymic', 'citizenship', 'passport_series', 'passport_number', 'center_id'])
            ->when($citizenship, function (Builder $query) use ($citizenship) {
                $query->where('citizenship', $citizenship);
            })

            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('id')
            ->lazyById(1000)
            ->each(function ($i) use ($handle, &$count) {
                
                fputcsv($handle, [
                    $i->surname,
                    $i->name,
                    $i->patronymic,
                    $i->citizenship,
                    $i->passport_series,
                    $i->passport_number,
                ]);
                $count++;
                $this->centerIsolationCheck::centerBelongs($i, $this->centerContext->id());
            });

        fclose($handle);
        
        Log::info('foreign_national_export', [
            'period' => [
                'from' => $dateFrom->format('d.m.Y'),
                'to' => $dateTo->format('d.m.Y'),
            ],
            'citizenship' => $citizenship,
            'count' => $count,
        ]);

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
