<?php

namespace App\Http\Controllers\Web\ExamDocument;

use App\Enums\ExamDocument;
use App\Exceptions\BusinessException;
use App\Models\Exam;
use App\Modules\ExamDocument\ExamDocumentRules;
use App\Modules\ExamDocument\ExamProtocolGenerator;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Livewire\Attributes\Authorize;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Where;

#[Middleware('auth:web')]
#[Prefix('exams/{exam}/documents')]
#[Where('exam', '[0-9]+')]
#[Authorize('protocol', 'exam')]

class ProtocolController
{
    public function __construct(
        protected ExamDocumentRules $examDocumentRules
    ) {}
        
    #[Get('protocol', 'exams.documents.protocol')]
    public function generate(
        Exam $exam,
        ExamProtocolGenerator $examProtocolGenerator
    ): Response {
        $exam->loadExists(
            $this->relations()
        );

        $this->ensureGenerationAvailable($exam);

        $pdf =  $examProtocolGenerator->execute($exam);

        return $pdf->stream(ExamDocument::Protocol->fileName(
            $exam->shortName(),
            $exam->beginTimeLocal()
        ));
    }
    
    #[Get('protocol/availability', 'exams.documents.protocol.availability')]
    public function availability(Exam $exam): JsonResponse
    {
        $exam->loadExists(
            $this->relations()
        );

        $this->ensureGenerationAvailable($exam);

        return response()->json([
            'redirectUrl' => route('exams.documents.protocol', [
                'exam' => $exam,
            ]),
        ]);
    }

    protected function relations(): array
    {
        return [
            'enrollments',
            'attempts',
            'enrollments as enrollments_with_no_attempts_exists' => function($query){
                return $query->whereDoesntHave('attempt');
            },
            'attempts as active_attempts_exists' => function ($query) {
                $query->active();
            }
        ];
    }

    protected function ensureGenerationAvailable(
        Exam $exam
    ):void {
        $result = $this->examDocumentRules->protocol($exam);
        
        if($result->isNotAvailable()){
            throw new BusinessException($result->code());
        }
    }
}
