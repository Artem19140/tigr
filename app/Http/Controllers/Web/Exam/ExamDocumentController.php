<?php

namespace App\Http\Controllers\Web\Exam;

use App\Domain\ExamDocument\ExamCodesGenerator;
use App\Domain\ExamDocument\ExamDocumentAvailable;
use App\Domain\ExamDocument\ExamProtocolGenerator;
use App\Domain\ExamDocument\ExamResultsGenerator;
use App\Enums\ExamDocument;
use App\Events\ExamDocumentGenerated;
use App\Models\Exam;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class ExamDocumentController
{
    public function __construct(
        protected ExamDocumentAvailable $examDocumentAvailable
    ) {}

    public function list(Exam $exam): Response
    {
        $this->examDocumentAvailable->list($exam);
        Gate::authorize('list', $exam);

        $exam->load(['foreignNationals', 'type']);

        $pdf = Pdf::loadView('pdf.exam.exam-foreign_nationals-list', [
            'foreignNationals' => $exam->foreignNationals,
            'exam' => $exam,
        ]);

        event(new ExamDocumentGenerated($exam, ExamDocument::List));
        $fileName = "Список_{$exam->short_name}_{$exam->begin_time_local->format('H-i_d.m.Y')}.pdf";
        
        return $pdf->stream($fileName);
    }

    public function listAvailable(Exam $exam): JsonResponse
    {
        $this->examDocumentAvailable->list($exam);

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
        $this->authorize($exam);
        $this->examDocumentAvailable->codes($exam);
        $fileName = "Кода_{$exam->short_name}_{$exam->begin_time_local->format('H-i_d.m.Y')}.pdf";
        $pdf = $examCodesGenerator->execute($exam);
        return $pdf->stream($fileName);
    }

    public function codesAvailable(Exam $exam): JsonResponse
    {
        $this->authorize($exam);
        $this->examDocumentAvailable->codes($exam);

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
        $this->examDocumentAvailable->protocol($exam);
        $fileName= "Протокол_{$exam->short_name}_{$exam->begin_time_local->format('H-i_d.m.Y')}.pdf";
        $pdf =  $examProtocolGenerator->execute($exam);
        return $pdf->stream($fileName);
    }

    public function protocolAvailable(Exam $exam): JsonResponse
    {
        $this->examDocumentAvailable->protocol($exam);

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
        $this->examDocumentAvailable->results($exam);
        $resultsPdf = $examResultsGenerator->execute($exam);
        $fileName = "Результаты_{$exam->short_name}_{$exam->begin_time->format('H-i_d.m.Y')}.pdf";

        return $resultsPdf->stream($fileName);
    }

    public function resultsAvailable(
        Exam $exam
    ): JsonResponse {
        $this->examDocumentAvailable->results($exam);

        return response()->json([
            'redirectUrl' => route('exam.documents.results', [
                'exam' => $exam,
            ]),
        ]);
    }

    protected function authorize(Exam $exam): void
    {
        Gate::authorize('examiner', $exam);
    }
}
