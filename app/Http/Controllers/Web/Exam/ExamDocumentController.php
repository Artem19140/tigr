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
        ])->setPaper('a4', 'landscape');

        event(new ExamDocumentGenerated($exam, ExamDocument::List, [
            'enrollments_ids' => $exam->enrollments->pluck('id')->toArray()
        ]));
        
        return $pdf->stream(ExamDocument::List->fileName(
            $exam->type->short_name,
            $exam->begin_time_local
        ));
    }

    public function listAvailable(Exam $exam): JsonResponse
    {
        $exam->loadExists('enrollments');
        $result = $this->examDocumentRules->list($exam);
        
        if($result->isNotAvailable()){
            throw new BusinessException($result->message());
        }

        return response()->json([
            'redirectUrl' => route('exams.documents.list', [
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

        $pdf = $examCodesGenerator->execute($exam);
        
        return $pdf->stream(ExamDocument::Codes->fileName(
            $exam->type->short_name,
            $exam->begin_time_local
        ));
    }

    public function codesAvailable(Exam $exam): JsonResponse
    {
        $exam->loadExists('enrollments');
        $result = $this->examDocumentRules->codes($exam);
        
        if($result->isNotAvailable()){
            throw new BusinessException($result->message());
        }

        return response()->json([
            'redirectUrl' => route('exams.documents.codes', [
                'exam' => $exam,
            ]),
        ]);
    }

    public function protocol(
        Exam $exam,
        ExamProtocolGenerator $examProtocolGenerator
    ): Response {
        
        $pdf =  $examProtocolGenerator->execute($exam);
        return $pdf->stream(ExamDocument::Protocol->fileName(
            $exam->type->short_name,
            $exam->begin_time_local
        ));
    }

    public function protocolAvailable(Exam $exam): JsonResponse
    {
        return response()->json([
            'redirectUrl' => route('exams.documents.protocol', [
                'exam' => $exam,
            ]),
        ]);
    }

    public function results(
        Exam $exam,
        ExamResultsGenerator $examResultsGenerator
    ): Response {
        $resultsPdf = $examResultsGenerator->execute($exam);

        return $resultsPdf->stream(ExamDocument::Results->fileName(
            $exam->type->short_name,
            $exam->begin_time_local
        ));
    }

    public function resultsAvailable(
        Exam $exam
    ): JsonResponse {

        return response()->json([
            'redirectUrl' => route('exams.documents.results', [
                'exam' => $exam,
            ]),
        ]);
    }
}