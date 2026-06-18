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
        $exam->loadCount('enrollments');
        $result = $this->examDocumentRules->list($exam);

        if($result->isNotAvailable()){
            throw new BusinessException($result->reason());
        }

        $exam->load(['foreignNationals', 'type']);

        $pdf = Pdf::loadView(ExamDocument::List->templatePath(), [
            'foreignNationals' => $exam->foreignNationals,
            'exam' => $exam,
        ]);

        event(new ExamDocumentGenerated($exam, ExamDocument::List));
        $fileName = "Список_{$exam->short_name}_{$exam->begin_time_local->format('H-i_d.m.Y')}.pdf";
        
        return $pdf->stream($fileName);
    }

    public function listAvailable(Exam $exam): JsonResponse
    {
        $exam->loadCount('enrollments');
        $result = $this->examDocumentRules->list($exam);
        
        if($result->isNotAvailable()){
            throw new BusinessException($result->reason());
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
        $exam->loadCount('enrollments');
        $result = $this->examDocumentRules->codes($exam);
        
        if($result->isNotAvailable()){
            throw new BusinessException($result->reason());
        }
        $fileName = "Кода_{$exam->short_name}_{$exam->begin_time_local->format('H-i_d.m.Y')}.pdf";
        $pdf = $examCodesGenerator->execute($exam);
        return $pdf->stream($fileName);
    }

    public function codesAvailable(Exam $exam): JsonResponse
    {
        $exam->loadCount('enrollments');
        $result = $this->examDocumentRules->codes($exam);
        
        if($result->isNotAvailable()){
            throw new BusinessException($result->reason());
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
        $fileName= "Протокол_{$exam->short_name}_{$exam->begin_time_local->format('H-i_d.m.Y')}.pdf";
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
        $fileName = "Результаты_{$exam->short_name}_{$exam->begin_time->format('H-i_d.m.Y')}.pdf";

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