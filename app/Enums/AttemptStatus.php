<?php

namespace App\Enums;

enum AttemptStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Finished = 'finished';
    case Annulled = 'annulled';
}
