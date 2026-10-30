<?php

namespace App\Modules\ExamDocument;

use App\Enums\ExamDocument;
use App\Events\ExamDocumentGenerated;
use App\Models\Exam;
use App\Models\ExamAccessCode;
use App\Modules\Enrollment\ExamCodeSaver;
use App\Modules\Shared\ExamSettings;
use App\Support\CodeGenerator;
use Barryvdh\DomPDF\Facade\Pdf;

final class ExamCodesGenerator
{
    public function __construct(
        protected ExamCodeSaver $codeSaver,
        protected CodeGenerator $codeGenerator
    ){}
    public function execute(Exam $exam)
    {
        $exam->load([
            'enrollments.examCode'
        ]);
        
        $this->generateCodesForExam($exam);
       
        $exam->load([
            'enrollments' => [
                'foreignNational',
                'examCode'
            ]
        ]);

        $exam->enrollments = $exam->enrollments
            ->sortBy('foreignNational.surname_normalized');

        $pdf = Pdf::loadView(ExamDocument::Codes->templatePath(), [
            'exam' => $exam,
        ]);

        event(new ExamDocumentGenerated($exam, ExamDocument::Codes));

        return $pdf;
    }

    protected function generateCodesForExam(Exam $exam): void
    {
        $expiredTime = $exam->begin_time->copy()->addMinutes(
            ExamSettings::codesTtlMinutes()
        );

        foreach ($exam->enrollments as $enrollment) {
            if ($enrollment->examCode) {
                continue;
            }

            $this->codeSaver->save(
                $enrollment,
                $this->codeGenerator->generate(), 
                $expiredTime,
                ExamAccessCode::INITIAL_TYPE
            );
        }
    }
}
