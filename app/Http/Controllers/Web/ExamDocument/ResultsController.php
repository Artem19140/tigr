<?php

namespace App\Http\Controllers\Web\ExamDocument;

use App\Enums\ExamDocument;
use App\Exceptions\BusinessException;
use App\Models\Exam;
use App\Modules\ExamDocument\ExamDocumentRules;
use App\Modules\ExamDocument\ExamResultsGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ResultsController
{
    public function __construct(
        protected ExamDocumentRules $examDocumentRules
    ) {}

    public function generate(
        Exam $exam,
        ExamResultsGenerator $examResultsGenerator
    ): Response {
        $exam->loadExists(
            $this->relations()
        );

        $this->ensureGenerationAvailable($exam);

        $resultsPdf = $examResultsGenerator->execute($exam);

        return $resultsPdf->stream(ExamDocument::Results->fileName(
            $exam->type->short_name,
            $exam->beginTimeLocal()
        ));
    }

    public function availability(
        Exam $exam
    ): JsonResponse {
        $exam->loadExists(
            $this->relations()
        );

        $this->ensureGenerationAvailable($exam);

        return response()->json([
            'redirectUrl' => route('exams.documents.results', [
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
            },
            'attempts as unreviewed_attempts_exists' => function ($query) {
                return $query->unreviewed();
            }
        ];
    }

    protected function ensureGenerationAvailable(
        Exam $exam
    ):void {
        $result = $this->examDocumentRules->results($exam);
        
        if($result->isNotAvailable()){
            throw new BusinessException($result->code());
        }
    }
}