<?php

namespace App\Modules\Shared;

final class ExamSettings{
    public static function codesLength():int
    {
        return config('system.codes_length');
    }

    public static function codesTtlMinutes():int
    {
        return config('system.codes_ttl');
    }

    public static function attemptMinDurationMinutes():int
    {
        return config('system.min_time_from_start_to_finish');
    }

    public static function enrollmentCloseBeforeExamMinutes(): int
    {
        return config('system.enrollment_window_closed_before_exam');
    }

}