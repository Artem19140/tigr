<?php

namespace App\Modules\ExamDocument;

use App\Modules\Center\CenterContext;
use App\Enums\ExamDocument;
use App\Events\ExamDocumentGenerated;
use App\Models\Attempt;
use App\Models\Exam;
use App\Support\CenterIsolationCheck;
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
                'attempt.center',
                'foreignNational',
            ],
        ]);
    }

    protected function getHeadersStatement(Exam $exam): Collection
    {
        return $exam->type->blocks->map(function ($block) {
            return [
                'id' => $block->id,
                'name' => $block->name,
                'subblocks' => $block->subblocks->map(function ($subblock) {
                    return [
                        'id' => $subblock->id,
                        'name' => $subblock->name,
                    ];
                }),
            ];
        });
    }

    protected function getRowsStatement(Exam $exam): Collection
    {
        $subblocks = $exam->type->blocks
            ->sortBy('order')
            ->flatMap(fn ($b) => $b->subblocks->sortBy('order'));

        return $exam->enrollments->map(function ($enrollment) use ($subblocks) {
            CenterIsolationCheck::centerBelongs($enrollment, app(CenterContext::class)->id());
            $attempt = $enrollment->attempt;
            $answers = $attempt?->attemptAnswers ?? collect();

            $answersBySubblock = $answers->groupBy(function ($a) {
                return $a->taskVariant?->task?->subblock_id;
            });

            $marksBySubblock = $subblocks->map(function ($subblock) use ($answersBySubblock) {
                $sum = $answersBySubblock->get($subblock->id)?->sum('mark');

                return ['sum' => $sum];
            });

            return [
                'fullName' => $enrollment->foreignNational->full_name,
                'fullPassport' => $enrollment->foreignNational->full_passport,
                'speakingStartedAt' => $attempt?->speaking_started_at_local?->format('H:i') ?? null,
                'speakingFinishedAt' => $attempt?->speaking_finished_at_local?->format('H:i') ?? null,
                'startedAt' => $attempt?->started_at_local?->format('H:i') ?? null,
                'finishedAt' => $attempt?->finished_at_local?->format('H:i') ?? null,
                'result' => $this->getAttemptResultStatus($attempt),
                'subblockMarks' => $marksBySubblock,
                'totalMark' => $attempt?->total_mark
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
                'answers' => $enrollment->attempt?->attemptAnswers->sortBy(function ($answer) {
                    return $answer->taskVariant?->task?->order ?? 0;
                }),
            ];
        });
    }
}
