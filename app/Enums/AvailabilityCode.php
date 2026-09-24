<?php

namespace App\Enums;

enum AvailabilityCode: string 
{
    case ExamCancelled = 'exam_cancelled';
    case ExamAlreadyStarted = 'exam_already_started';
    case ExamCodeExpired = 'exam_code_expired';
    case EnrollmentNotExists = 'enrollment_not_exists';
    case ExamPending = 'exam_pending';
    case AttemptExists = 'attempt_exists';
    case AttemptsNotExists = 'attempts_not_exists';
    case ActiveAttemptsExists = 'active_attemtps_exists';
    case AttemptAnnulled = 'attempt_annulled';
    case ExamOnReview = 'exam_on_review';
    case ExamCodeAliveAndEnrollmentsWithNoAttemptsExists = 'exam_codes_alive_and_exist_enrollments_with_no_attempt';
}
