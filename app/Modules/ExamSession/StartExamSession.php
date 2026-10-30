<?php

namespace App\Modules\ExamSession;

use App\Enums\BusinessCode;
use App\Exceptions\BusinessException;
use App\Models\Attempt;
use App\Models\AttemptAnswer;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Modules\Exam\ExamAttributesInitializer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

final class StartExamSession
{
    public function __construct(
        protected ExamAttributesInitializer $examAttributesInitializer,
        protected ExamVariantGenerator $examVariantGenerator
    ) {}
    public function execute(
        Enrollment $enrollment
    ): Attempt {
        return DB::transaction(function() use($enrollment) {
            $exam = Exam::find($enrollment->exam_id);

            if(! $exam){
                Log::critical('exam_not_found', [
                    'enrollment_id' => $enrollment->id,
                    'exam_id' => $enrollment->exam_id
                ]);
                throw new ModelNotFoundException();
            }

            $this->ensureExamInValidState($exam);

            $attempt = $this->createAttempt($enrollment);

            $examVariant = $this->examVariantGenerator
                ->execute($exam, $attempt);

            AttemptAnswer::insert($examVariant);

            $this->examAttributesInitializer->execute($exam);

            $enrollment->examCode->makeUsed();

            $attempt->start();
            $attempt->save();

            return $attempt;
        });
    }

    protected function ensureExamInValidState(
        Exam $exam
    ): void{
        if($exam->isCancelled()){
            throw ValidationException::withMessages([
                'code' => 'Экзамен отменен'
            ]);
        }

        if ($exam->begin_time->isFuture()) {
            throw ValidationException::withMessages([
                'code' => 'Экзамен еще не начался'
            ]);
        }
    }

    protected function createAttempt(
        Enrollment $enrollment
    ): Attempt {
        $hasAttempt = Attempt::where('enrollment_id', $enrollment->id)
            ->exists();

        if ($hasAttempt) {
            Log::warning(BusinessCode::ActiveAttemptsExists->value, [
                'enrollment_id' => $enrollment->id,
                'exam_id' => $enrollment->exam_id,
            ]);
            
            throw new BusinessException(
                BusinessCode::ActiveAttemptsExists
            );
        }

        return Attempt::create([
            'foreign_national_id' => $enrollment->foreign_national_id,
            'enrollment_id' => $enrollment->id,
            'exam_id' => $enrollment->exam_id
        ]);
    }
}