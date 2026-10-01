<?php

namespace App\Modules\Report;

use App\Exceptions\BusinessException;
use App\Models\Attempt;
use App\Modules\Shared\CenterData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class EnsureFrdoGenerationAvailable
{
    public function execute(string $date, string $type): void
    {
        $date = Carbon::parse($date)->setTimezone(CenterData::timeZome());
        //Что все экзамены проведены!
        $this->ensureAttemptsExists($date);
        $this->ensureNoActiveAttempts($date);
        $this->ensureAllAttemptsReviewed($date);
        $this->ensureHasDataForReportType($date, $type);
    }

    protected function ensureAttemptsExists(Carbon $date): void
    {
        $attemptsExists = $this->query($date)
            ->exists();

        if (! $attemptsExists) {
            $formattedDate = $date->copy()->format('d.m.Y');
            throw new BusinessException("Попыток экзамена за $formattedDate нет");
        }

    }

    protected function ensureNoActiveAttempts(Carbon $date): void
    {
        $activeAttemptsExists = $this->query($date)
            ->active()
            ->exists();

        if ($activeAttemptsExists) {
            $formattedDate = $date->copy()->format('d.m.Y');
            throw new BusinessException("Некоторые попытки за $formattedDate еще активны");
        }

    }

    protected function ensureAllAttemptsReviewed(Carbon $date): void
    {
        $unreviewedAttemptsExists = $this->query($date)
            ->unreviewed()
            ->exists();

        if ($unreviewedAttemptsExists) {
            $formattedDate = $date->copy()->format('d.m.Y');
            throw new BusinessException("Не все попытки за $formattedDate проверены");
        }

    }

    protected function ensureHasDataForReportType(
        Carbon $date,
        string $type
    ): void {
        $attemptsForReportExists = $this->query($date)
            ->whereNotNull('reviewed_at')
            ->when($type === 'certificates', function(Builder $query) {
                $query->passed();
            })
            ->when($type === 'references', function(Builder $query) {
                $query->failed();
            })->exists();

        if (! $attemptsForReportExists) {
            $reportName = $type === 'certificates' ? 'сертификатов' : 'справок';
            $date = $date->copy()->format('d.m.Y');
            throw new BusinessException("Данных для $reportName за $date нет");
        }
    }

    protected function query(Carbon $date): Builder
    {
        return Attempt::query()
            ->whereBetween('created_at', [
                $date->copy()->startOfDay()->utc(),
                $date->copy()->endOfDay()->utc(),
            ]);
    }
}