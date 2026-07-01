<?php

namespace App\Http\Controllers\Web\Exam;

use App\Modules\ExamDocument\ExamCodesGenerator;
use App\Modules\ExamDocument\ExamDocumentRules;
use App\Modules\ExamDocument\ExamProtocolGenerator;
use App\Modules\ExamDocument\ExamResultsGenerator;
use App\Enums\ExamDocument;
use App\Events\ExamDocumentGenerated;
use App\Exceptions\BusinessException;
use App\Models\Exam;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ExamDocumentController
{
    public function __construct(
        protected ExamDocumentRules $examDocumentRules
    ) {}

    public function list(Exam $exam): Response
    {
        $exam->loadExists('enrollments');
        $result = $this->examDocumentRules->list($exam);

        if($result->isNotAvailable()){
            throw new BusinessException($result->message());
        }

        $exam->load(['foreignNationals', 'type']);

        $pdf = Pdf::loadView(ExamDocument::List->templatePath(), [
            'foreignNationals' => $exam->foreignNationals,
            'exam' => $exam,
        ]);

        event(new ExamDocumentGenerated($exam, ExamDocument::List, [
            'enrollments_ids' => $exam->enrollments->pluck('id')->toArray()
        ]));

        $fileName = "Список_{$exam->type->short_name}_{$exam->begin_time_local->format('H-i_d.m.Y')}.pdf";
        
        return $pdf->stream($fileName);
    }

    public function listAvailable(Exam $exam): JsonResponse
    {
        $exam->loadExists('enrollments');
        $result = $this->examDocumentRules->list($exam);
        
        if($result->isNotAvailable()){
            throw new BusinessException($result->message());
        }

        return response()->json([
            'redirectUrl' => route('exam.documents.list', [
                'exam' => $exam,
            ]),
        ]);
    }

    public function codes(
        Exam $exam,
        ExamCodesGenerator $examCodesGenerator
    ): Response {
        $exam->loadExists('enrollments');
        $result = $this->examDocumentRules->codes($exam);
        
        if($result->isNotAvailable()){
            throw new BusinessException($result->message());
        }
        $fileName = "Кода_{$exam->type->short_name}_{$exam->begin_time_local->format('H-i_d.m.Y')}.pdf";
        $pdf = $examCodesGenerator->execute($exam);
        return $pdf->stream($fileName);
    }

    public function codesAvailable(Exam $exam): JsonResponse
    {
        $exam->loadExists('enrollments');
        $result = $this->examDocumentRules->codes($exam);
        
        if($result->isNotAvailable()){
            throw new BusinessException($result->message());
        }

        return response()->json([
            'redirectUrl' => route('exam.documents.codes', [
                'exam' => $exam,
            ]),
        ]);
    }

    public function protocol(
        Exam $exam,
        ExamProtocolGenerator $examProtocolGenerator
    ): Response {
        $fileName= "Протокол_{$exam->type->short_name}_{$exam->begin_time_local->format('H-i_d.m.Y')}.pdf";
        
        $pdf =  $examProtocolGenerator->execute($exam);
        return $pdf->stream($fileName);
    }

    public function protocolAvailable(Exam $exam): JsonResponse
    {
        return response()->json([
            'redirectUrl' => route('exam.documents.protocol', [
                'exam' => $exam,
            ]),
        ]);
    }

    public function results(
        Exam $exam,
        ExamResultsGenerator $examResultsGenerator
    ): Response {
        $resultsPdf = $examResultsGenerator->execute($exam);
        $fileName = "Результаты_{$exam->type->short_name}_{$exam->begin_time->format('H-i_d.m.Y')}.pdf";

        return $resultsPdf->stream($fileName);
    }

    public function resultsAvailable(
        Exam $exam
    ): JsonResponse {

        return response()->json([
            'redirectUrl' => route('exam.documents.results', [
                'exam' => $exam,
            ]),
        ]);
    }
}