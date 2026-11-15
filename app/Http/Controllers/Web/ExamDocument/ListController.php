<?php

namespace App\Http\Controllers\Web\ExamDocument;

use App\Enums\ExamDocument;
use App\Events\ExamDocumentGenerated;
use App\Exceptions\BusinessException;
use App\Models\Exam;
use App\Modules\ExamDocument\ExamDocumentRules;
use App\Modules\ExamDocument\ExamListDataBuilder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Livewire\Attributes\Authorize;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Where;

#[Middleware('auth:web')]
#[Prefix('exams/{exam}/documents')]
#[Where('exam', '[0-9]+')]
#[Authorize('list', 'exam')]

class ListController
{
    public function __construct(
        protected ExamDocumentRules $examDocumentRules,
        protected ExamListDataBuilder $builder
    ) {}

    #[Get('list', 'exams.documents.list')]
    public function generate(Exam $exam): Response
    {
        $exam->loadExists('enrollments');

        $this->ensureGenerationAvailable($exam);
        
        $exam->load([
            'enrollments.foreignNational' => function($query){
                $query->orderBy('foreign_nationals.surname_normalized');
            }, 
            'type'
        ]);

        $build = $this->builder->build($exam);

        $pdf = Pdf::loadView(ExamDocument::List->templatePath(), [
            'headers' => $build['headers'],
            'rows' => $build['rows'],
            'exam' => $exam,
        ])->setPaper('a4', 'landscape');

        event(new ExamDocumentGenerated($exam, ExamDocument::List, [
            'enrollments_ids' => $exam->enrollments->pluck('id')->toArray()
        ]));
        
        return $pdf->stream(ExamDocument::List->fileName(
            $exam->shortName(),
            $exam->beginTimeLocal()
        ));
    }

    #[Get('list/availability', 'exams.documents.list.availability')]
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
