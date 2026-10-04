<?php

namespace App\Enums;

enum BusinessCode: string 
{
    case ExamCancelled = 'exam_cancelled';
    case ExamAlreadyStarted = 'exam_already_started';
    case ExamAlreadyFinished = 'exam_already_finished';
    case ExamCodeExpired = 'exam_code_expired';
    case ExamPending = 'exam_pending';
    case PendingExamsExists = 'exam_pending_exists';
    case ExamsConflict = 'exams_conflict';
    case ExamOnReview = 'exam_on_review';
    case ProtocolCommentEditUnavailable = 'protocol_comment_edit_unavailable';
    case ExaminersBusy = 'examiners_busy';
    case ExaminersNotWork = 'examiners_not_work';
    case HasNoRoleExaminer = 'has_no_role_examier';


    case EnrollmentNotExists = 'enrollment_not_exists';
    case EnrollmentAlreadyExists = 'enrollment_already_exists';
    case EnrollmentWindowClosed = 'enrollment_window_closed';
    case EnrollmentsConflict = 'enrollments_conflict';
    case EnrollmentFull = 'enrollment_full';
    
    
    case AttemptExists = 'attempt_exists';
    case AttemptsNotExists = 'attempts_not_exists';
    case ActiveAttemptsExists = 'active_attemtps_exists';
    case AttemptAnnulled = 'attempt_annulled';
    case UnreviewedAttemptsExists = 'unreviewed_attempts_exists';
    case AttemptAnnulmentUnavailable = 'attempt_annulment_unavailable';
    case AttemptAlreadyReviwed = 'attempt_already_reviewed';
    case AttemptAlreadyFinished = 'attempt_already_finished';
    case UnreviewedAnswersExists = 'unreviewed_answers_exists';
    case AttemptNotStarted = 'attempt_not_started';
    case AttemptEarlyFinish = 'attempt_early_finish';


    case ExamCodeAliveAndEnrollmentsWithNoAttemptsExists = 'exam_codes_alive_and_exist_enrollments_with_no_attempt';
    

    case CodesUnavailable = 'codes_unavailable';
    case CodesTtlExpired = 'codes_ttl_expired';


    case NoData = 'no_data';
    case NoDataForReport = 'no_data_for_report';

    case SpeakingUnavailable = 'speaking_unavailable';
    case SpeakingAlreadyFinished = 'speaking_already_finished';
    case SpeakingAlreadyStarted = 'speaking_already_started';
    case SpeakingNotStarted = 'speaking_not_started';
    case AttemptHasNoSpeaking = 'attempt_has_no_speaking';

    case EmployeeAlreadyFired = 'employee_already_fired';
}
