<?php

namespace App\Enums;

enum ExamStatus: string
{
    case Pending = 'pending';
    case Going = 'going';
    case Finished = 'finished';
    case Cancelled = 'cancelled';
}
