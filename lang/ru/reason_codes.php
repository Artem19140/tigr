<?php

use App\Enums\AvailabilityCode;

return [
    AvailabilityCode::ExamCancelled->value => 'Экзамен отменен',
    AvailabilityCode::EnrollmentNotExists->value => 'На экзамен отсутвует запись',
    AvailabilityCode::ExamPending->value => 'Экзамен еще не начался',
    AvailabilityCode::AttemptsNotExists->value => 'Нет попыток экзамена',
    AvailabilityCode::ActiveAttemptsExists->value => 'Существуют активные попытки экзамена',
    AvailabilityCode::ExamAlreadyFinished->value => 'Экзамен завершен',
    AvailabilityCode::AttemptExists->value => 'Существует попытка экзамена',
    AvailabilityCode::ExamAlreadyStarted->value => 'Экзамен уже начался',
    AvailabilityCode::AttemptAnnulled->value => 'Попытка аннулирована',
    'exam_on_checking' => 'Идет проверка результатов',
    'codes_available_only_on_exam_day' => 'Кода доступны только в день экзамена',
    'codes_ttl_expired'=>'Срок действия кодов истек',
    'attempt_can_be_annuled_only_on_attempt_day' => 'Аннулировать попытку возможно только в день ее прохождения',
    'attempt_speaking_already_started' => 'Говорение уже начато',
    'attempt_speaking_not_started_yet' => 'Говорение не начато',
    'attempt_has_no_speaking' => 'У данной попытки нет заданий на говорение',
    'attempt_speaking_already_finished' => 'Говорение уже завершено',
    'attempt_speaking_available_on_attempt_passing_day' => 'Говорение доступно в день прохождения попытки',
    'protocol_comment_edit_available_only_on_exam_day' => 'Редактировать комментарий возможно только в день экзамена'
];