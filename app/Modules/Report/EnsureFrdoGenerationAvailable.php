<?php

namespace App\Modules\Report;

use App\Exceptions\BusinessException;
use App\Models\Attempt;
use App\Modules\Shared\CenterData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class EnsureFrdoGenerationAvailable
{
    public function execute(string $examDate, string $type): void
    {
        $examDate = Carbon::parse($examDate)->setTimezone(CenterData::timeZome());

        $this->ensureAttemptsExists($examDate);
        $this->ensureNoActiveAttempts($examDate);
        $this->ensureAllAttemptsChecked($examDate);
        $this->ensureHasDataForReportType($examDate, $type);
    }

    protected function ensureAttemptsExists(Carbon $examDate): void
    {
        $attemptsExists = $this->query($examDate)
            ->exists();

        if (! $attemptsExists) {
            $formattedDate = $examDate->copy()->format('d.m.Y');
            throw new BusinessException("Попыток экзамена за $formattedDate нет");
        }

    }

    protected function ensureNoActiveAttempts(Carbon $examDate): void
    {
        $activeAttemptsExists = $this->query($examDate)
            ->active()
            ->exists();

        if ($activeAttemptsExists) {
            $formattedDate = $examDate->copy()->format('d.m.Y');
            throw new BusinessException("Некоторые попытки за $formattedDate еще активны");
        }

    }

    protected function ensureAllAttemptsChecked(Carbon $examDate): void
    {
        $uncheckedAttemptsExists = $this->query($examDate)
            ->unchecked()
            ->exists();

        if ($uncheckedAttemptsExists) {
            $formattedDate = $examDate->copy()->format('d.m.Y');
            throw new BusinessException("Не все попытки за $formattedDate проверены");
        }

    }

    protected function ensureHasDataForReportType(
        Carbon $examDate,
        string $type
    ): void {
        $attemptsForReportExists = $this->query($examDate)
            ->whereNotNull('checked_at')
            ->when($type === 'certificates', function(Builder $query) {
                $query->passed();
            })
            ->when($type === 'references', function(Builder $query) {
                $query->failed();
            })->exists();

        if (! $attemptsForReportExists) {
            $reportName = $type === 'certificates' ? 'сертификатов' : 'справок';
            $date = $examDate->copy()->format('d.m.Y');
            throw new BusinessException("Данных для $reportName за $date нет");
        }
    }

    protected function query(Carbon $examDate): Builder
    {
        return Attempt::query()
            ->whereBetween('created_at', [
                $examDate->copy()->startOfDay()->utc(),
                $examDate->copy()->endOfDay()->utc(),
            ]);
    }
}
