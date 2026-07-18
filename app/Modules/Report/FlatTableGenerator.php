<?php

namespace App\Modules\Report;

use App\Enums\ReportType;
use App\Enums\TaskType;
use App\Events\ReportGenerated;
use App\Models\Attempt;
use App\Modules\Shared\CenterData;
use App\Support\CsvWriter;
use Carbon\Carbon;

class FlatTableGenerator
{
    public function __construct(
        protected CsvWriter $csvWriter
    ) {}

    public function execute(
        Carbon $dateFrom,
        Carbon $dateTo
    ) {
        $this->csvWriter->setHeaders($this->headers());
        $strNumber = 1;

        Attempt::query()
            ->with(['foreignNational', 'exam.type', 'answers' => [
                'taskVariant.task',
                'answer',
            ]])
            ->whereBetween('created_at', [
                $dateFrom->copy()->setTimezone(CenterData::timeZome())->startOfDay()->utc(),
                $dateTo->copy()->setTimezone(CenterData::timeZome())->endOfDay()->utc(),
            ])

            ->whereNotNull('checked_at')

            ->chunkById(300, function ($attempts) use (&$strNumber) {
                foreach ($attempts as $attempt) {
                    $answers = $attempt->answers->sortBy(fn ($a) => $a->taskVariant->task->order);
                    foreach ($answers as $answer) {
                        $taskVariant = $answer->taskVariant;
                        $task = $taskVariant?->task;

                        $answerInTable = '';

                        if ($task->type === TaskType::SingleChoice) {
                            $answerInTable = $answer->answer['order'] ?? '';
                        }
                        
                        // if ($task->type === TaskType::TextInput) {
                        //     $answerInTable = $answer->answer;
                        // }

                        $this->csvWriter->writeRow( [
                            $strNumber,
                            $attempt->exam->type->level,
                            $attempt->foreign_national_id,
                            $attempt->id,
                            $taskVariant->task->order,
                            $taskVariant->fipi_number,
                            $answerInTable,
                            $answer->mark ?? 0,
                            $attempt->total_mark,
                            $attempt->isPassed() ? 'да' : 'нет',
                        ]);
                        $strNumber++;
                    }
                }
            });

        event(new ReportGenerated(ReportType::FlatTable, [
            'period' => [
                'from' => $dateFrom->copy()->format('d.m.Y'),
                'to' => $dateTo->copy()->format('d.m.Y')
            ],
            'count_attempts' => $strNumber
        ]));
    }

    protected function headers(): array
    {
        return [
            'StrNumber',
            'Level',
            'ParticipantID',
            'HumanTestID',
            'TaskNumber',
            'TaskID',
            'Answer',
            'Mark',
            'TotalMark',
            'Resolution',
        ];
    }
}
