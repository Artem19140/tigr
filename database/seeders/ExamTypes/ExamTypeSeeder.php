<?php

namespace Database\Seeders\ExamTypes;

use App\Models\Answer;
use App\Models\Block;
use App\Models\ExamType;
use App\Models\Subblock;
use App\Models\Task;
use App\Models\TaskVariant;

class ExamTypeSeeder
{   
    public function run(
        ExamType $examType,
        array $blocks
    )
    {
        $orderTask = 1;
        $orderBlock = 1;
        foreach ($blocks as $block) {
            $blockCreated = Block::firstOrCreate(
                [
                    'order' => $orderBlock,
                    'exam_type_id' => $examType->id,
                ]  , 
                [
                    'exam_type_id' => $examType->id,
                    'min_mark' => $block['min_mark'],
                    'name' => $block['name'],
                    'order' => $orderBlock,
                ]
            );
            $orderBlock += 1;
            $orderSubblock = 1;
            foreach ($block['subblocks'] as $subblock) {
                $subblockCreated = Subblock::firstOrCreate(
                    [
                        'block_id' => $blockCreated->id,
                        'order' => $orderSubblock
                    ],
                    [
                        'block_id' => $blockCreated->id,
                        'name' => $subblock['name'],
                        'min_mark' => $subblock['min_mark'],
                        'order' => $orderSubblock,
                    ]
                );
                $orderSubblock += 1;
                foreach ($subblock['tasks'] as $task) {
                    $taskCreated = Task::firstOrCreate(
                        [
                            'order' => $orderTask,
                            'subblock_id' => $subblockCreated->id,
                        ],
                        [
                            'order' => $orderTask,
                            'subblock_id' => $subblockCreated->id,
                            'type' => $task['type'],
                            'mark' => $task['mark'],
                            'description' => $task['description'] ?? null,
                            'checking_mode' => $task['checking_mode'] ?? null
                        ]
                    );
                    foreach ($task['variants'] as $variant) {
                        $taskVariantCreated = TaskVariant::firstOrCreate(
                        [
                            'fipi_number' => $variant['fipi_number']
                        ],
                        [
                            'content' => $variant['content'],
                            'fipi_number' => $variant['fipi_number'],
                            'group_number' => $variant['group_number'] ?? null,
                            'task_id' => $taskCreated->id,
                        ]);
                        $orderAnswer = 1;
                        foreach ($variant['answers'] ?? [] as $answer) {
                            Answer::firstOrCreate(
                                [
                                    'order' => $orderAnswer,
                                    'task_variant_id' => $taskVariantCreated->id
                                ],
                                [
                                    'content' => $answer['content'],
                                    'is_correct' => $answer['is_correct'],
                                    'order' => $orderAnswer,
                                    'task_variant_id' => $taskVariantCreated->id
                                ]
                            );
                            $orderAnswer += 1;
                        }
                    }
                    $orderTask += 1;
                }
            }
        }

    }
}