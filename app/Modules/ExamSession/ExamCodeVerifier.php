<?php

namespace App\Modules\ExamSession;

use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamAccessCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ExamCodeVerifier
{
    public function execute(string $codeString): Enrollment
    {
        $code = $this->findCode($codeString);
        $enrollment = $code->enrollment;
        
        $this->ensureNotCancelled($enrollment);
        $this->ensureExamInValidState($enrollment->exam);
        $this->ensureCodeNotExpired($code);
        $this->ensureCodeNotUsed($code);
        $this->ensureHasPayment($enrollment->hasPayment());

        return $enrollment;
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

    protected function findCode(string $code): ExamAccessCode
    {
        $code = ExamAccessCode::query()
            ->with('enrollment.exam')
            ->firstWhere('code', $code);

        if (! $code) {
            throw ValidationException::withMessages([
                'code' => 'Код не найден',
            ]);
        }

        return $code;
    }

    protected function ensureCodeNotExpired(ExamAccessCode $code): void
    {
        if ($code->isExpired()) {
            throw ValidationException::withMessages([
                'code' => 'Истек срок действия кода',
            ]);
        }
    }

    protected function ensureCodeNotUsed(ExamAccessCode $code): void
    {
        if ($code->isUsed()) {
            throw ValidationException::withMessages([
                'code' => 'Код использован',
            ]);
        }
    }

    protected function ensureHasPayment(bool $hasPayment): void
    {
        if (! $hasPayment) {
            throw ValidationException::withMessages([
                'code' => 'Экзамен не оплачен',
            ]);
        }
    }

    protected function ensureNotCancelled(Enrollment $enrollment): void
    {
        if($enrollment->isCancelled()){
            Log::warning('Enrollment cancelled, but code is in use', [
                'enrollment' => $enrollment->id,
            ]);
            
            throw ValidationException::withMessages([
                'code' => 'Запись отменена',
            ]);
        }
    }
}
