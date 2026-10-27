<?php

namespace App\Modules\ExamDocument;

use App\Enums\ExamDocument;
use App\Events\ExamDocumentGenerated;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class ExamScoresDataBuilder
{
    public function build(
        Exam $exam
    ) {
        $this->loadRelations($exam);
        event(new ExamDocumentGenerated($exam, ExamDocument::Scores));
        return [
            'headers' => $this->getHeaders($exam),
            'rows' => $this->getRows($exam)
        ];
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

        $exam->enrollments = $exam->enrollments
            ->sortBy('foreignNational.surname_normalized');
    }

    protected function getHeaders(Exam $exam): array
    {
        $headers = [
            'fullName' => 'ФИО',
            'totalMark' => 'Общий балл'
        ];
        for($i = 1; $i <= $exam->tasksCount(); $i++){
            $headers[] = $i;
        }

        return $headers;
    }

    protected function getRows(Exam $exam): Collection
    {
        return $exam->enrollments->map(function ($enrollment) use($exam) {
            $row = [
                'fullName' => $enrollment->foreignNational->full_name,
                $enrollment->attempt?->total_mark ?? ''
            ];

            $marks = $enrollment->attempt?->attemptAnswers
                ->sortBy(function ($answer) {
                    return $answer->taskVariant?->task?->order ?? 0;
                })
                ->map(fn ($answer) =>  $answer->mark ?? 0);

            if($marks === null){
                
                $empty = [];
                for($i=1; $i <= $exam->tasksCount(); $i++){
                    $empty[] = '';
                }

                return [
                    ...$row,
                    ...$empty
                ];
            }
            return [
                ...$row,
                ...$marks
            ];
            
        });
    }
}