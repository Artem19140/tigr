<?php

namespace App\Modules\Shared;

final class ExamSettings{
    public static function codesLength():int
    {
        return config('exam.codes_length');
    }

    public static function codesTtlMinutes():int
    {
        return config('exam.codes_ttl');
    }

    public static function attemptMinDurationMinutes():int
    {
        return config('exam.min_time_from_start_to_finish');
    }

    public static function enrollmentCloseBeforeExamMinutes(): int
    {
        return config('exam.enrollment_window_closed_before_exam');
    }

    public static function minAgeYear():int
    {
        return config('exam.min_age');
    }

    public static function minTimeBeforeCreateMinutes():int
    {
        return config('exam.min_time_before_exam_creating');
    }

}