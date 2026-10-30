<?php

use App\Http\Resources\ExamType\ExamTypeResource;
use App\Http\Resources\ExamType\ExamTypeShowResource;
use App\Http\Resources\TaskVariant\TaskVariantResource;
use App\Models\ExamType;
use App\Models\Task;
use Inertia\Inertia;

Route::prefix('exams-types')
    ->group(function(){

        Route::get('', function(){
            return Inertia::render('ExamTypes/Index', [
                'types' => ExamTypeResource::collection(ExamType::all())
            ]);
        })->name('exams-types.index');

        Route::get('{exam_type}', function(ExamType $examType){
            $examType->load('blocks.subblocks.tasks');
            return Inertia::render('ExamTypes/View', [
                'type' => new ExamTypeShowResource($examType)
            ]);
        })->name('blocks.index');

        
    });

    Route::get('tasks/{task}/variants', function(Task $task){
        $task->load([
            'variants' => ['answers', 'task']
        ]);
        return Inertia::render('ExamTypes/TaskView', [
            'task' => TaskVariantResource::collection($task->variants)
        ]);
    })->name('tasks.show');