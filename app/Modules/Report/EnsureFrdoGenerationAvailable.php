<?php

namespace App\Modules\Report;

use App\Enums\BusinessCode;
use App\Exceptions\BusinessException;
use App\Models\Attempt;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Modules\Shared\CenterData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class EnsureFrdoGenerationAvailable
{
    public function execute(
        string $date, 
        string $type
    ): void {
        
        $date = Carbon::parse($date)->setTimezone(
            CenterData::timeZome()
        );

        $this->ensureAllExamsConducted($date);
        $this->ensureAttemptsExists($date);
        $this->ensureNoActiveAttempts($date);
        $this->ensureAllAttemptsReviewed($date);
        $this->ensureHasDataForReportType($date, $type);
    }

    protected function ensureAllExamsConducted(
        Carbon $date
    ): void {

        $start = Carbon::now();
        $end = Carbon::now()
            ->setTimezone(
                CenterData::timeZome()
            )
            ->endOfDay()
            ->utc();

        $pendingExamsExists = Exam::query()
            ->notCancelled()
            ->whereBetween('begin_time', [
                $start,
                $end
            ])
            ->exists();
        
        $enrollmentsWithNotExpiredCodeExists = Enrollment::query()
            ->whereNotNull('exam_code')
            ->whereBetween('exam_code_expired_at', [
                $start,
                $end
            ])->exists();

        $notAllExamsConducted = $pendingExamsExists || $enrollmentsWithNotExpiredCodeExists;

        if($notAllExamsConducted){
            throw new BusinessException(
                BusinessCode::PendingExamsExists,
                [
                    'date' => $date->copy()->format('d.m.Y')
                ]
            );
        }
    }

    protected function ensureAttemptsExists(
        Carbon $date
    ): void {
        $attemptsExists = $this->query($date)
            ->exists();

        if (! $attemptsExists) {
            throw new BusinessException(
                BusinessCode::NoData
            );
        }

    }

    protected function ensureNoActiveAttempts(
        Carbon $date
    ): void {
        $activeAttemptsExists = $this->query($date)
            ->active()
            ->exists();

        if ($activeAttemptsExists) {
            throw new BusinessException(
                BusinessCode::ActiveAttemptsExists
            );
        }

    }

    protected function ensureAllAttemptsReviewed(
        Carbon $date
    ): void {
        $unreviewedAttemptsExists = $this->query($date)
            ->unreviewed()
            ->exists();

        if ($unreviewedAttemptsExists) {
            throw new BusinessException(
                BusinessCode::UnreviewedAttemptsExists
            );
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

            throw new BusinessException(
                BusinessCode::NoDataForReport,
                [
                    'report' => $reportName
                ]
            );
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