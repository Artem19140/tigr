<?php

use App\Enums\BusinessCode;

return [
    BusinessCode::ExamCancelled->value => 'Экзамен отменен',
    BusinessCode::ExamPending->value => 'Экзамен еще не начался',
    BusinessCode::ExamAlreadyFinished->value => 'Экзамен уже завершен',
    BusinessCode::ExamAlreadyStarted->value => 'Экзамен уже начался',
    BusinessCode::ExamOnReview->value => 'Идет проверка результатов',
    BusinessCode::ExamsConflict->value => "В это время по данному адресу уже проводится экзамен по :name в :time",
    BusinessCode::ExaminersBusy->value => 'Выбранные экзаменаторы недоступны в указанное время :names',
    BusinessCode::ExaminersNotWork->value => ':names уже не работает(-ют) в центре',
    BusinessCode::HasNoRoleExaminer->value => ':names не имеет(-ют) роли экзаменатора',
    BusinessCode::ProtocolCommentEditUnavailable->value => 'Редактировать комментарий возможно только в день экзамена',


    BusinessCode::CodesUnavailable->value => 'Кода доступны только в день экзамена',
    BusinessCode::ExamCodeExpired->value => 'Код для начала экзамена не был использован',
    BusinessCode::CodesTtlExpired->value => 'Срок действия кодов истек',


    BusinessCode::EnrollmentNotExists->value => 'На экзамен отсутвует запись',
    BusinessCode::EnrollmentWindowClosed->value => 'Запись закрывается за :min минут до начала экзамена',
    BusinessCode::EnrollmentFull->value => 'Запись на экзамен полная',
    BusinessCode::EnrollmentAlreadyExists->value => 'Запись на экзамен уже сущестует',
    BusinessCode::EnrollmentsConflict->value => 'ИГ имеет парралельные записи на экзамен',


    BusinessCode::AttemptsNotExists->value => 'Нет попыток экзамена',
    BusinessCode::ActiveAttemptsExists->value => 'Существуют активные попытки экзамена',
    BusinessCode::UnreviewedAttemptsExists->value => 'Существуют непроверенные попытки экзамена',
    BusinessCode::AttemptExists->value => 'Существует попытка экзамена',
    BusinessCode::AttemptAnnulled->value => 'Попытка аннулирована',
    BusinessCode::AttemptAnnulmentUnavailable->value => 'Аннулировать попытку возможно только в день ее прохождения',
    BusinessCode::AttemptNotStarted->value => 'Попытка еще не начата',
    BusinessCode::AttemptAlreadyFinished->value => 'Попытка уже завершена',
    BusinessCode::AttemptEarlyFinish->value => 'Попытку возможно завершить минимум через :min минут после начала',
    BusinessCode::AttemptAlreadyReviwed->value => 'Попытка уже проверена',
    BusinessCode::UnreviewedAnswersExists->value => 'Существуют непроверенные ответы',
    

    BusinessCode::SpeakingAlreadyStarted->value => 'Говорение уже начато',
    BusinessCode::SpeakingNotStarted->value => 'Говорение не начато',
    BusinessCode::AttemptHasNoSpeaking->value => 'У данной попытки нет заданий на говорение',
    BusinessCode::SpeakingAlreadyFinished->value => 'Говорение уже завершено',
    BusinessCode::SpeakingUnavailable->value => 'Говорение доступно в день прохождения попытки',


    BusinessCode::NoData->value => 'Нет данных для выгрузки',
    BusinessCode::NoDataForReport->value => 'Нет данных для :report',

    BusinessCode::EmployeeAlreadyFired->value => 'Сотрудник уже уволен'
];