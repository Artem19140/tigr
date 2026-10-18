<?php

namespace App\Modules\ExamDocument;

use App\Models\Attempt;
use App\Models\Block;
use App\Models\Exam;
use App\Models\Subblock;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class ExamStatementDataBuilder
{
    public function build(
        Exam $exam
    ): array {
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
    protected function getAttemptResultStatus(?Attempt $attempt): string
    {
        if (! $attempt) {
            return 'н/я';
        }

        if($attempt->isAnnulled()){
            return 'Справка';
        }

        return $attempt->isPassed() ? 'Сертификат' : 'Справка';
    }


    protected function getHeaders(Exam $exam): array
    {
        $headers = [
            [
                'label' => 'ФИО',
                'rowspan' => 2
            ],
            [
                'label' => 'ФИО латиница',
                'rowspan' => 2
            ],
            [
                'label' => 'Паспортные данные',
                'rowspan' => 2
            ],
            [
                'label' => 'Гражданство',
                'rowspan' => 2
            ],
            [
                'label' => 'Время экзамена',
                'colspan' => 2,
                'childrens' => [
                    [
                        'label' => 'Начала'
                    ],
                    [
                        'label' => 'Конца'
                    ]
                ]
            ]
        ];

        if($exam->hasSpeaking()){
            $headers[] = [
                'label' => 'Время устной части',
                'colspan' => 2,
                'childrens' => [
                    [
                        'label' => 'Начала'
                    ],
                    [
                        'label' => 'Конца'
                    ]
                ]
            ];
        }
        
        $blocksHeaders = $exam->type->blocks->map(function (Block $block)  {
            $reportableSubblocks = $block->subblocks
                ->whereNotNull('min_mark')
                ->map(function ($subblock) {
                    return [
                        'label' => $subblock->name,
                    ];
                });

            $hasMultipleSubblocks = $block->subblocks->count() > 1;
            
            if( $hasMultipleSubblocks ){
                $reportableSubblocks->push(['label' => 'Общий балл']);
            }

            return [
                'label' => $block->name,
                'childrens' => $hasMultipleSubblocks  
                    ? $reportableSubblocks->values()->all() 
                    : [],
                'colspan' => $hasMultipleSubblocks  
                    ?  $reportableSubblocks->count()
                    : 1,
                'rowspan' => $hasMultipleSubblocks  ? 1 : 2
            ];

            
        })->toArray();

        $resultsHeaders = [
            [
                'label' => 'Общий балл',
                'rowspan' => 2
            ],
            [
                'label' => 'Результат (документ)',
                'rowspan' => 2
            ]
        ];
        
        return array_merge($headers, $blocksHeaders, $resultsHeaders);
    }

    protected function getRows(Exam $exam)
    {        
        $rows = [];


        $rows = $exam->enrollments
            ->map(function ($enrollment) use ($exam) {
                $attempt = $enrollment->attempt;

                $answers = $attempt?->attemptAnswers ?? collect();

                $answersBySubblock = $answers->groupBy(
                    fn ($answer) => $answer->taskVariant?->task?->subblock_id
                );

                $row = [
                    $enrollment->foreignNational->full_name,
                    $enrollment->foreignNational->full_name_latin,
                    $enrollment->foreignNational->full_passport,
                    $enrollment->foreignNational->citizenship,
                    $attempt?->started_at_local?->format('H:i'),
                    $attempt?->finished_at_local?->format('H:i'),
                ];

                if($exam->hasSpeaking()){
                    $row[] = $attempt?->speaking_started_at_local?->format('H:i');

                    $row[] = $attempt?->speaking_finished_at_local?->format('H:i');
                }

                $marksByBlocks = $exam->type->blocks
                    ->map(function (Block $block) use ($answersBySubblock) {
                        $result = [];

                        $subblocksMarks = $block->subblocks
                            ->map(function (Subblock $subblock) use ($answersBySubblock) {
                                return [
                                    'total' => $answersBySubblock
                                        ->get($subblock->id, collect())
                                        ->sum('mark'),

                                    'min_mark' => $subblock->min_mark,
                                ];
                            });

                        if ($block->subblocks->count() > 1) {
                            $result['total'] = $subblocksMarks->sum('total');
                        }

                        $result['subblocksMarks'] = $subblocksMarks
                            ->whereNotNull('min_mark')
                            ->values();

    
                        return $result;
                    });

                foreach($marksByBlocks as $block){
                    
                    foreach($block['subblocksMarks'] as $subblock){
                        $row[] = $subblock['total'];
                    
                    }

                    if( isset($block['total']) ){
                        $row[] = $block['total'];
                    }
                }

                $row = [...$row, ...[
                        $attempt?->total_mark ?? 0,

                        $this->getAttemptResultStatus($attempt),
                    ]
                ];
                
                return $row;
            });

        return $rows;
    }    
}