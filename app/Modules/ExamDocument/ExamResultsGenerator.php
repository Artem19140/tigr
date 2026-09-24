<?php

namespace App\Modules\ExamDocument;

use App\Enums\ExamDocument;
use App\Events\ExamDocumentGenerated;
use App\Models\Attempt;
use App\Models\Block;
use App\Models\Exam;
use App\Models\Subblock;
use App\Modules\Shared\CenterData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class ExamResultsGenerator
{
    public function execute(Exam $exam): \Barryvdh\DomPDF\PDF
    {
        $this->loadRelations($exam);

        event(new ExamDocumentGenerated($exam, ExamDocument::Results));

        return Pdf::loadView(ExamDocument::Results->templatePath(), [
            'exam' => $exam,
            'center' => new CenterData(),
            'statementTable' => [
                'headers' => $this->getHeadersStatement($exam),
                'rows' => $this->getRowsStatement($exam),
            ],
            'markTable' => [
                'rows' => $this->getRowsMarksTable($exam),
            ],
        ])->setPaper('a4', 'landscape');
    }

    public function loadRelations(Exam $exam): void
    {
        $exam->loadMissing([
            'type' => [
                'blocks' => fn (HasMany $q) => $q->orderBy('order'),
                'blocks.subblocks' => fn (HasMany $q) => $q->orderBy('order'),
            ],
            'enrollments' => [
                'attempt.attemptAnswers.taskVariant.task',
                'foreignNational',
            ],
        ]);
    }

    protected function getHeadersStatement(Exam $exam): Collection
    {
        
        return $exam->type->blocks->map(function ($block) {

            $filteredSubblocks = $block->subblocks
                ->whereNotNull('min_mark')
                ->map(function ($subblock) {
                    return [
                        'id' => $subblock->id,
                        'name' => $subblock->name,
                    ];
                });
 
            $filteredSubblocksCount = $filteredSubblocks->count();

            if( $filteredSubblocksCount > 1 ){
                $filteredSubblocks->push(['name' => 'Сум.']);
            }

            return [
                'id' => $block->id,
                'name' => $block->name,
                'subblocks' => $filteredSubblocks,
                'colspan' => $filteredSubblocksCount > 1 ? $filteredSubblocksCount + 1 : 1
            ];
        });
    }

    protected function getRowsStatement(Exam $exam): Collection
    {        
        return $exam->enrollments->map(function ($enrollment) use ($exam) {
            $attempt = $enrollment->attempt;

            $answers = $attempt?->attemptAnswers ?? collect();

            $answersBySubblock = $answers->groupBy(
                fn ($answer) => $answer->taskVariant?->task?->subblock_id
            );

            $marksByBlocks = $exam->type->blocks->map(function (Block $block) use ($answersBySubblock) {
                $result = [];

                $marksBySubblocks = $block->subblocks->map(function (Subblock $subblock) use ($answersBySubblock) {
                    return [
                        'total' => $answersBySubblock
                            ->get($subblock->id, collect())
                            ->sum('mark'),

                        'min_mark' => $subblock->min_mark,
                    ];
                });

                if ($block->subblocks->count() > 1) {
                    $result['total'] = $marksBySubblocks->sum('total');
                }

                $result['marksBySubblocks'] = $marksBySubblocks
                    ->whereNotNull('min_mark')
                    ->values();

                return $result;
            });

            return [
                'fullName' => $enrollment->foreignNational->full_name,

                'fullPassport' => $enrollment->foreignNational->full_passport,

                'startedAt' => $attempt?->started_at_local?->format('H:i') ?? null,

                'finishedAt' => $attempt?->finished_at_local?->format('H:i') ?? null,

                'speakingStartedAt' => $attempt?->speaking_started_at_local?->format('H:i') ?? null,

                'speakingFinishedAt' => $attempt?->speaking_finished_at_local?->format('H:i') ?? null,

                'marksByBlocks' => $marksByBlocks,
                
                'totalMark' => $attempt?->total_mark,

                'result' => $this->getAttemptResultStatus($attempt),
            ];
        });
    }

    protected function getAttemptResultStatus(?Attempt $attempt): string
    {
        if (! $attempt) {
            return 'н/я';
        }

        return $attempt->isPassed() ? 'Сертификат' : 'Справка';
    }

    protected function getRowsMarksTable(Exam $exam): Collection
    {
        return $exam->enrollments->map(function ($enrollment) {
            return [
                'fullName' => $enrollment->foreignNational->full_name,
                'fullPassport' => $enrollment->foreignNational->full_passport,
                'answers' => $enrollment->attempt?->attemptAnswers
                    ->sortBy(function ($answer) {
                        return $answer->taskVariant?->task?->order ?? 0;
                    }),
            ];
        });
    }
}