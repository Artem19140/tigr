<?php

namespace App\Enums;

enum ExamResultStatus: string
{
    case Annulled = 'annulled';
    case Absent = 'absent';
    case Failed = 'failed';
    case Passed = 'passed';
}
