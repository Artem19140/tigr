<?php

namespace App\Modules\Attempt\Action;

use App\Models\Task;
use App\Modules\Attempt\Services\VerifyCodeService;
use App\Modules\Counter\GroupNumberGenerator;
use App\Modules\Counter\SessionNumberGenerator;
use App\Exceptions\BusinessException;
use App\Models\Attempt;
use App\Models\AttemptAnswer;
use App\Models\Enrollment;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateAttemptAction
{
    public function __construct(
        protected GroupNumberGenerator $groupNumberGenerator,
        protected SessionNumberGenerator $sessionNumberGenerator,
        protected VerifyCodeService $verifyCodeService
    ) {}

    public function execute(string $code): Attempt
    {
        $attempt = DB::transaction(function () use ($code) {
            $enrollment = $this->verifyCodeService->execute($code);

            $exam = Exam::findOrFail($enrollment->exam_id);
            
            if($exam->isCancelled()){
                throw new BusinessException('Экзамен отменен');
            }

            if (! $exam->isGoing()) {
                throw new BusinessException('Ввести код возможно только во время экзамена');
            }

            $attempt = $this->createAttempt($enrollment);

            $examVariant = $this->generateExamVariant($exam, $attempt);

            AttemptAnswer::insert($examVariant);

            $this->initializeExamAttributes($exam);

            return $attempt;
        });

        return $attempt;
    }

    protected function createAttempt(
        Enrollment $enrollment
    ): Attempt {
        $hasAttempt = Attempt::where('enrollment_id', $enrollment->id)
            ->exists();

        if ($hasAttempt) {
            Log::warning('trying to create second attempt per exam', [
                'enrollment_id' => $enrollment->id,
                'exam_id' => $enrollment->exam_id,
            ]);
            throw new BusinessException('Сущестует текущая попытка экзамен');
        }

        return Attempt::create([
            'foreign_national_id' => $enrollment->foreign_national_id,
            'enrollment_id' => $enrollment->id,
            'exam_id' => $enrollment->exam_id,
            'center_id' => $enrollment->center_id,
        ]);
    }

    protected function generateExamVariant(
        Exam $exam,
        Attempt $attempt
    ): array {        
        $tasks = Task::query()
            ->select(['id', 'subblock_id', 'type', 'is_active', 'description', 'postscriptum', 'order'])
            ->whereHas(
                'subblock.block.examType', function (Builder $query) use ($exam){
                    $query->where('id',  $exam->exam_type_id);
                }
            )
            ->with(['variants:id,group_number,task_id'])
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
                'attempt_id' => $attempt->id,
                'center_id' => $exam->center_id,
            ];
        }

        return $examVariant;
    }

    protected function initializeExamAttributes(Exam $exam): void
    {
        $needSave = false;

        if (! $exam->group) {
            $needSave = true;
            $exam->group = $this->groupNumberGenerator->execute();
        }
        if (! $exam->session) {
            $needSave = true;
            $exam->session = $this->sessionNumberGenerator->execute();
        }
        
        if ($needSave) {
            $exam->save();
        }
    }
}
