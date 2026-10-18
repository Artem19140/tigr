<?php

namespace App\Modules\ExamDocument;

use App\Enums\ExamDocument;
use App\Events\ExamDocumentGenerated;
use App\Models\Exam;
use App\Modules\Shared\CenterData;
use Barryvdh\DomPDF\Facade\Pdf;

class ExamResultsPdfGenerator
{
    public function __construct(
        protected ExamStatementDataBuilder $statementBuilder,
        protected ExamScoresDataBuilder $scoresBuilder
    ){}
    
    public function execute(Exam $exam): \Barryvdh\DomPDF\PDF
    {
        $statementBuild = $this->statementBuilder->build($exam);
        $scoresBuild = $this->scoresBuilder->build($exam);

        event(new ExamDocumentGenerated($exam, ExamDocument::Results));

        return Pdf::loadView(ExamDocument::Results->templatePath(), [
            'exam' => $exam,
            'center' => new CenterData(),
            'statementTable' => [
                'headers' => $statementBuild['headers'], 
                'rows' => $statementBuild['rows'], 
            ],
            'markTable' => [
                'headers' => $scoresBuild['headers'],
                'rows' => $scoresBuild['rows'],
            ],
        ])->setPaper('a4', 'landscape');
    }
}