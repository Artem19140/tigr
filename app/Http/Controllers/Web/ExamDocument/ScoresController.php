<?php

namespace App\Http\Controllers\Web\ExamDocument;

use App\Enums\ExamDocument;
use App\Exceptions\BusinessException;
use App\Models\Exam;
use App\Modules\ExamDocument\ExamDocumentRules;
use App\Modules\ExamDocument\ExamScoresDataBuilder;
use App\Modules\Shared\CenterData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ScoresController
{
    public function __construct(
        protected ExamDocumentRules $examDocumentRules
    ) {}

    public function generate(
        Exam $exam,
        ExamScoresDataBuilder $builder
    ): Response {
        $exam->loadExists(
            $this->relations()
        );

        $this->ensureGenerationAvailable($exam);

        $build = $builder->build($exam);

        return Pdf::loadView(ExamDocument::Scores->templatePath(), [
            'rows' => $build['rows'],
            'headers' => $build['headers'],
            'exam' => $exam,
            'center' => new CenterData(),
        ])
            ->setPaper('a4', 'landscape')
            ->stream(ExamDocument::Scores->fileName(
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
            'redirectUrl' => route('exams.documents.scores', [
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
