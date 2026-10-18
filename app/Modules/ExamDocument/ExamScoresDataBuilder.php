<?php

namespace App\Modules\ExamDocument;

use App\Models\Exam;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class ExamScoresDataBuilder
{
    public function build(
        Exam $exam
    ) {
        $this->loadRelations($exam);

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
            'ФИО',
            'Паспортные данные',
            'Гражданство'
        ];
        for($i = 1; $i <= $exam->tasksCount(); $i++){
            $headers[] = $i;
        }

        return $headers;
    }

    protected function getRows(Exam $exam): Collection
    {
        return $exam->enrollments->map(function ($enrollment) {
            return [
                'fullName' => $enrollment->foreignNational->full_name,
                'fullNameLatin' => $enrollment->foreignNational->full_name_latin,
                'fullPassport' => $enrollment->foreignNational->full_passport,
                'citizenship' =>  $enrollment->foreignNational->citizenship,
                'answers' => $enrollment->attempt?->attemptAnswers
                    ->sortBy(function ($answer) {
                        return $answer->taskVariant?->task?->order ?? 0;
                    }),
            ];
        });
    }
}