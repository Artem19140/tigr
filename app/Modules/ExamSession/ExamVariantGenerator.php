<?php

namespace App\Modules\ExamSession;

use App\Models\Attempt;
use App\Models\Exam;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;

class ExamVariantGenerator
{
    public function execute(
        Exam $exam,
        Attempt $attempt
    ): array {
        $tasks = Task::query()
            ->select([
                'id', 
                'subblock_id', 
                'type', 
                'is_active', 
                'order'
            ])
            ->whereHas(
                'subblock.block.examType', function (Builder $query) use ($exam){
                    $query->where('id',  $exam->exam_type_id);
                }
            )
            ->with([
                'variants:id,group_number,task_id'
            ])
            ->get();

        $groups = [];
        $examVariant = [];
        
        foreach ($tasks as $task) {
            
            $variants = $task->variants;
            
            $variant = $variants
                ->whereIn('group_number', $groups)
                ->first();

            if (! $variant) {
                $variant = $variants->random();
            }
            
            if ($variant->group_number && ! \in_array($variant->group_number, $groups)) {
                $groups[] = $variant->group_number;
            }

            $examVariant[] = [
                'exam_id' => $exam->id,
                'task_variant_id' => $variant->id,
                'attempt_id' => $attempt->id
            ];
        }

        return $examVariant;
    }
    
}