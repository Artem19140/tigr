<?php

namespace App\Modules\Enrollment\Action;

use App\Modules\Counter\RegNumberGenerator;
use App\Exceptions\BusinessException;
use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ForeignNational;
use App\Modules\Shared\SystemSettings;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

final class CreateEnrollmentAction
{
    public function __construct(
        protected RegNumberGenerator $regNumberGenerator
    ) {}

    public function execute(
        int $examId,
        int $foreignNationalId,
        Employee $creator,
        bool $hasPayment
    ): Enrollment {
        $exam = Exam::findOrFail($examId);
        $foreignNational = ForeignNational::findOrFail($foreignNationalId);

        $this->ensureCreatingAvailable($exam, $foreignNational);

        $enrollment = Enrollment::create([
            'reg_number' => $this->regNumberGenerator->execute(),
            'creator_id' => $creator->id,
            'center_id' => $exam->center_id,
            'has_payment' => $hasPayment,
            'exam_id' => $exam->id,
            'foreign_national_id' => $foreignNational->id,
        ]);

        return $enrollment;
    }

    protected function ensureCreatingAvailable(
        Exam $exam,
        ForeignNational $foreignNational
    ): void {
        if($exam->isCancelled()){
            throw new BusinessException('Экзамен отменен');
        }
        $this->ensureEnrollementWindowNotClosed($exam);
        $this->ensureEnrollmentNotExists($exam, $foreignNational);
        $this->ensureParallellEnrollmentsNotExists($exam, $foreignNational);
        $this->ensureNotFullEnrollment($exam);
    }

    protected function ensureEnrollementWindowNotClosed(Exam $exam): void
    {
        $closeBeforeMinutes = SystemSettings::enrollmentCloseBeforeExamMinutes();
        $enrollmentEnded = Carbon::now()
            ->greaterThan($exam->begin_time->subMinutes($closeBeforeMinutes));
        if ($enrollmentEnded) {
            throw new BusinessException(
                "Запись закрывается за $closeBeforeMinutes минут до начала экзамена"
            );
        }
    }

    protected function ensureParallellEnrollmentsNotExists(
        Exam $exam,
        ForeignNational $foreignNational
    ): void {
        $parallellEnrollmentsExists = Exam::query()
            ->where('begin_time', '<', $exam->end_time)
            ->where('end_time', '>', $exam->begin_time)
            ->notCancelled()
            ->whereHas('enrollments', function (Builder $query) use ($foreignNational) {
                $query->where('foreign_national_id', $foreignNational->id);
            })
            ->exists();

        if ($parallellEnrollmentsExists) {
            throw new BusinessException('ИГ имеет парралельные записи на экзамен');
        }
    }

    public function ensureNotFullEnrollment(
        Exam $exam
    ): void {
        $enrollmentsCount = $exam->enrollments()->count();
        if ($exam->capacity <= $enrollmentsCount) {
            throw new BusinessException('Запись на экзамен полная');
        }
    }

    public function ensureEnrollmentNotExists(
        Exam $exam,
        ForeignNational $foreignNational
    ): void {
        $exists = Enrollment::query()
            ->where('exam_id', $exam->id)
            ->where('foreign_national_id', $foreignNational->id)
            ->exists();

        if ($exists) {
            throw new BusinessException('Запись на экзамен уже сущестует');
        }
    }
}
