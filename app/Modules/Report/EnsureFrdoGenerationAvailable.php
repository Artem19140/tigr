<?php

namespace App\Modules\Report;

use App\Modules\Center\CenterContext;
use App\Exceptions\BusinessException;
use App\Models\Attempt;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class EnsureFrdoGenerationAvailable
{
    public function __construct(
        protected CenterContext $centerContext
    ) {}

    public function execute(string $examDate, bool $success): void
    {
        $examDate = Carbon::parse($examDate);
        $this->ensureAttemptsExists($examDate);
        $this->ensureNoActiveAttempts($examDate);
        $this->ensureAllAttemptsChecked($examDate);
        $this->ensureHasDataForReportType($examDate, $success);
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
        bool $success
    ): void {
        $query = $this->query($examDate)
            ->whereNotNull('checked_at');
        $success ? $query->passed() : $query->failed();
        
        $attemptsForReportExists = $query->exists();

        if (! $attemptsForReportExists) {
            $reportName = $success ? 'сертификатов' : 'справок';
            $date = $examDate->format('d.m.Y');
            throw new BusinessException("Данных для $reportName за $date нет");
        }
    }

    protected function query(Carbon $examDate): Builder
    {
        return Attempt::query()
            ->forCenter($this->centerContext->id())
            ->whereBetween('created_at', [
                $examDate->copy()->startOfDay(),
                $examDate->copy()->endOfDay(),
            ]);
    }
}
