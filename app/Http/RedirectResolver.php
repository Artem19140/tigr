<?php

namespace App\Http;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RedirectResolver
{
    public function execute(): string
    {
        $webGuard = Auth::guard('web');

        if ($webGuard->check()) {
            $employee = $webGuard->user();

            return $employee->resolveRedirect();
        }

        $foreignNationalGuard = Auth::guard('foreignNationals');

        if(!$foreignNationalGuard->check()){
            Log::critical('No authenticated user in supported guards');
            abort(500);
        }

        $foreignNational = $foreignNationalGuard->user();

        $enrollment = $foreignNational->latestEnrollment;

        if(! $enrollment){
            Log::critical('Foreign national has no enrollment', [
                'foreign_national_id' => $foreignNational->id,
            ]);

            $foreignNationalGuard->logout();

            return route('login');
        }

        $attempt = $enrollment->attempt;

        if(! $attempt){
            Log::info('Redirecting to exam session setup...', [
                'enrollment_id' => $enrollment->id
            ]);

            return  route('exams-sessions.sound-setting', [
                'enrollment' => $enrollment
            ]);
        }

        Log::info('Redirect to attempt', [
            'attempt_id' => $attempt->id,
            'attempt_status' => $attempt->status,
            'enrollment' => $enrollment->id
        ]);

        return route('attempts.show', ['attempt' => $attempt]);
    }
}