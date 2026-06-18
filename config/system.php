<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Code length
    |-------------------------------------------------------------------------
    | The system generates exam codes with fixed length
    | Codes consist only of digits 
    | Here you can change it
    | *@var int*
    */

    'codes_length' => 6,

     /*
    |--------------------------------------------------------------------------
    | Exam Codes TTL
    |--------------------------------------------------------------------------
    | The system generates codes with TTL in minutes, it works like:
    |                    exam begin_time + TTL
    | after that moment codes will be expired
    | This restriction need due to safety.
    |
    */

    'codes_ttl' => 60,

    /*
    |--------------------------------------------------------------------------
    | Enrollment Window Restriction Before Exam Start
    |--------------------------------------------------------------------------
    | Enrollment will be disabled N minutes before the exam begins
    | This restriction prevents accidental or unauthorized last-minute enrollments.
    |
    */

    'enrollment_window_closed_before_exam' => 10,

    /*
    |--------------------------------------------------------------------------
    | Exam Creation Time Constraint
    |--------------------------------------------------------------------------
    | The system allows creating an exam no later than N minutes before 
    | its start time.
    | This restriction ensures that unexpected errors or last-minute changes
    | in exam settings do not disrupt the testing process.
    |
    */

    'min_time_before_exam_creating' => 180,

    /*
    |--------------------------------------------------------------------------
    | Attempt Finish Time Constraint
    |--------------------------------------------------------------------------
    | The system allows finish an attempt no earlier than N minutes after 
    | its start time.
    | This restriction need due to law.
    |
    */

    'min_time_from_start_to_finish' => 10,

    /*
    |--------------------------------------------------------------------------
    | Data storage TTL
    |--------------------------------------------------------------------------
    | Foreign National personal data, exam results etc must be storage only 
    | fixed period due to law
    | Period specifies in years
    | This restriction need due to law.
    |
    */

    'data_storage_ttl' => 3,

    /*
    |--------------------------------------------------------------------------
    | Foreign National Age Constraint
    |--------------------------------------------------------------------------
    | Foreign National can pass the exam only from 18 years old
    | age specifies in years
    | This restriction need due to law.
    |
    */

    'min_age' => 18,
];