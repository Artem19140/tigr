<?php

namespace App\Modules\ExamSession;

use App\Models\Enrollment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ExamCodeVerifier
{
    public function execute(string $code): Enrollment
    {
        $enrollment = $this->findOrFailEnrollmentByCode($code);
        $this->ensureNotCancelled($enrollment);
        $this->ensureCodeNotExpired($enrollment->exam_code_expired_at);
        $this->ensureCodeNotUsed($enrollment);
        $this->ensureHasPayment($enrollment->hasPayment());

        return $enrollment;
    }

    protected function findOrFailEnrollmentByCode(string $code): Enrollment
    {
        $enrollment = Enrollment::where('exam_code', $code)
            ->first();

        if (! $enrollment) {
            throw ValidationException::withMessages([
                'code' => 'Код не найден',
            ]);
        }

        return $enrollment;
    }

    protected function ensureCodeNotExpired(Carbon $expiredAt): void
    {
        if ($expiredAt < Carbon::now()) {
            throw ValidationException::withMessages([
                'code' => 'Истек срок действия кода',
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

    protected function ensureCodeNotUsed(Enrollment $enrollment): void
    {
        if ($enrollment->exam_code_used_at !== null) {
            Log::warning('Exam code repeat used', [
                'enrollment' => $enrollment->id,
            ]);
            throw ValidationException::withMessages([
                'code' => 'Код использован',
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
