<?php

namespace App\Http\Controllers\Web\ExamDocument;

use App\Enums\ExamDocument;
use App\Exceptions\BusinessException;
use App\Models\Exam;
use App\Modules\ExamDocument\ExamDocumentRules;
use App\Modules\ExamDocument\ExamStatementDataBuilder;
use App\Modules\ExamDocument\StatementXlsxGenerator;
use App\Modules\Shared\CenterData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Where;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Middleware('auth:web')]
#[Prefix('exams/{exam}/documents')]
#[Where('exam', '[0-9]+')]
#[Authorize('results', 'exam')]

class StatementController
{
    public function __construct(
        protected ExamDocumentRules $examDocumentRules
    ) {}

    #[Get('statement', 'exams.documents.statement')]
    public function generate(
        Request $request,
        Exam $exam,
        ExamStatementDataBuilder $builder,
        StatementXlsxGenerator $xlsxGenerator
    ): Response | StreamedResponse {
        $request->validate([
            'type' => ['required', 'string', 'in:pdf,xlsx']
        ]);
        
        $exam->loadExists(
            $this->relations()
        );

        $this->ensureGenerationAvailable($exam);

        $build = $builder->build($exam);

        $type = $request->input('type');

        if($type === 'xlsx') {
            
            $fileName = ExamDocument::Statement->fileName(
                $exam->shortName(),
                $exam->beginTimeLocal(),
                'xlsx'
            );
            $writer = $xlsxGenerator->generate($exam);

            return new StreamedResponse(function () use ($writer) {
                $writer->save('php://output');
            }, 200, $this->getXlsxHeaders($fileName));
        }
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

    #[Get('statement/availability', 'exams.documents.statement.availability')]
    public function availability(
        Request $request,
        Exam $exam
    ): JsonResponse {
        $request->validate([
            'type' => ['required', 'string', 'in:pdf,xlsx']
        ]);

        $exam->loadExists(
            $this->relations()
        );

        $this->ensureGenerationAvailable($exam);

        return response()->json([
            'redirectUrl' => route('exams.documents.statement', [
                'exam' => $exam,
                'type' => $request->input('type')
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

    protected function getXlsxHeaders(string $fileName):array{
        return [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment;filename=$fileName",
            'Cache-Control' => 'max-age=0',
        ];
    }
}
