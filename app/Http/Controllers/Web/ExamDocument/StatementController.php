<?php

namespace App\Http\Controllers\Web\ExamDocument;

use App\Enums\ExamDocument;
use App\Exceptions\BusinessException;
use App\Models\Exam;
use App\Modules\ExamDocument\ExamDocumentRules;
use App\Modules\ExamDocument\ExamStatementDataBuilder;
use App\Modules\Shared\CenterData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class StatementController
{
    public function __construct(
        protected ExamDocumentRules $examDocumentRules
    ) {}

    public function generate(
        Exam $exam,
        ExamStatementDataBuilder $builder
    ): Response {
        $exam->loadExists(
            $this->relations()
        );

        $this->ensureGenerationAvailable($exam);

        $build = $builder->build($exam);

        return Pdf::loadView(ExamDocument::Statement->templatePath(), [
            'rows' => $build['rows'],
            'headers' => $build['headers'],
            'exam' => $exam,
            'center' => new CenterData(),
        ])
            ->setPaper('a4', 'landscape')
            ->stream(ExamDocument::Statement->fileName(
                $exam->shortName(),
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
            'redirectUrl' => route('exams.documents.statement', [
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
