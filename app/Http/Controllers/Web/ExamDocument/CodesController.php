<?php

namespace App\Http\Controllers\Web\ExamDocument;

use App\Enums\ExamDocument;
use App\Exceptions\BusinessException;
use App\Models\Exam;
use App\Modules\ExamDocument\ExamCodesGenerator;
use App\Modules\ExamDocument\ExamDocumentRules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CodesController
{
    public function __construct(
        protected ExamDocumentRules $examDocumentRules
    ) {}
    public function generate(
        Exam $exam,
        ExamCodesGenerator $examCodesGenerator
    ): Response {
        $exam->loadExists('enrollments');

        $this->ensureGenerationAvailable($exam);

        $pdf = $examCodesGenerator->execute($exam);
        
        return $pdf->stream(ExamDocument::Codes->fileName(
            $exam->type->short_name,
            $exam->beginTimeLocal()
        ));
    }

    public function availability(Exam $exam): JsonResponse
    {
        $exam->loadExists('enrollments');

        $this->ensureGenerationAvailable($exam);

        return response()->json([
            'redirectUrl' => route('exams.documents.codes', [
                'exam' => $exam,
            ]),
        ]);
    }

    protected function ensureGenerationAvailable(
        Exam $exam
    ):void {
        $result = $this->examDocumentRules->codes($exam);
        
        if($result->isNotAvailable()){
            throw new BusinessException($result->code());
        }
    }
}
