<?php

namespace App\Http\Controllers\Web\ExamDocument;

use App\Enums\ExamDocument;
use App\Events\ExamDocumentGenerated;
use App\Exceptions\BusinessException;
use App\Models\Exam;
use App\Modules\ExamDocument\ExamDocumentRules;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ListController
{
    public function __construct(
        protected ExamDocumentRules $examDocumentRules
    ) {}
    public function generate(Exam $exam): Response
    {
        $exam->loadExists('enrollments');

        $this->ensureGenerationAvailable($exam);

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
            $exam->beginTimeLocal()
        ));
    }

    public function availability(Exam $exam): JsonResponse
    {
        $exam->loadExists('enrollments');

        $this->ensureGenerationAvailable($exam);

        return response()->json([
            'redirectUrl' => route('exams.documents.list', [
                'exam' => $exam,
            ]),
        ]);
    }

    protected function ensureGenerationAvailable(
        Exam $exam
    ):void {
        $result = $this->examDocumentRules->list($exam);
        
        if($result->isNotAvailable()){
            throw new BusinessException($result->code());
        }
    }
}
