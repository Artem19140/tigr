<?php

namespace App\Http\Controllers\Web\ExamDocument;

use App\Enums\ExamDocument;
use App\Exceptions\BusinessException;
use App\Models\Exam;
use App\Modules\ExamDocument\ExamCodesGenerator;
use App\Modules\ExamDocument\ExamDocumentRules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Where;

#[Middleware('auth:web')]
#[Prefix('exams/{exam}/documents')]
#[Where('exam', '[0-9]+')]
#[Authorize('codes', 'exam')]

class CodesController
{
    public function __construct(
        protected ExamDocumentRules $examDocumentRules
    ) {}
    
    #[Get('codes', 'exams.documents.codes')]
    public function generate(
        Exam $exam,
        ExamCodesGenerator $examCodesGenerator
    ): Response {
        $exam->loadExists('enrollments');

        $this->ensureGenerationAvailable($exam);

        $pdf = $examCodesGenerator->execute($exam);
        
        return $pdf->stream(ExamDocument::Codes->fileName(
            $exam->shortName(),
            $exam->beginTimeLocal()
        ));
    }
    
    #[Get('codes/availability', 'exams.documents.codes.availability')]
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
