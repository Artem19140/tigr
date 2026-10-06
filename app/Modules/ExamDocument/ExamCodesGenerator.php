<?php

namespace App\Modules\ExamDocument;

use App\Enums\ExamDocument;
use App\Events\ExamDocumentGenerated;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Modules\Shared\ExamSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

final class ExamCodesGenerator
{
    public function execute(Exam $exam)
    {
        $exam->load(['enrollments.foreignNational']);
        
        $exam->enrollments = $exam->enrollments
            ->sortBy('foreignNational.surname_normalized');

        $this->generateCodesForExam($exam);

        $pdf = Pdf::loadView(ExamDocument::Codes->templatePath(), [
            'exam' => $exam,
        ]);

        event(new ExamDocumentGenerated($exam, ExamDocument::Codes));

        return $pdf;
    }

    protected function generateCodesForExam(Exam $exam): void
    {
        foreach ($exam->enrollments as $enrollment) {
            if ($this->codeWasGenerated($enrollment)) {
                continue;
            }
            $this->generateAndSaveUniqueCode($enrollment, $exam);
        }
    }

    protected function generateAndSaveUniqueCode(
        Enrollment $enrollment,
        Exam $exam
    ): void {
        $expiredTime = $exam->begin_time->copy()->addMinutes(
            ExamSettings::codesTtlMinutes()
        );
        while (true) {
            try {
                $this->saveCode(
                    $enrollment, 
                    $this->generateCode(), 
                    $expiredTime
                );

                return;
            } catch (QueryException $e) {
                if ($e->getCode() !== '23505') {
                    throw $e;
                }
            }
        }
    }

    protected function codeWasGenerated(Enrollment $enrollment): bool
    {
        return $enrollment->exam_code_used_at || $enrollment->exam_code_expired_at;
    }

    protected function generateCode(): string
    {
        $length = ExamSettings::codesLength();
        $max = (10 ** $length) - 1;
        $rnd = random_int(0, $max);
        $code = str_pad($rnd, $length, '0', STR_PAD_LEFT);

        return $code;
    }

    protected function saveCode(
        Enrollment $enrollment,
        string $code,
        Carbon $expiredTime
    ): void {
        $enrollment->update([
            'exam_code_expired_at' => $expiredTime,
            'exam_code' => $code
        ]);
    }
}
