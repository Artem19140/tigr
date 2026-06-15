<?php

namespace App\Enums;

enum AvailabilityCode: string
{
    case ExamCancelled = 'exam_cancelled';
    case ExamStarted = 'exam_started';
    case ExamFinished = 'exam_finished';
    case EnrollmentNotExists = 'enrollment_not_exists';
    case ExamPending = 'exam_pending';
    case AttemptExists = 'attempt_exists';
    case AttemptsNotExists = 'attempts_not_exists';
    case ActiveAttemptsExists = 'active_attemtps_exists';
    case AttemptAnnulled = 'attempt_annulled';
}
