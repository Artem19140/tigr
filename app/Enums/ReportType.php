<?php

namespace App\Enums;

use Carbon\Carbon;

enum ReportType: string
{
    case Frdo = 'frdo';
    case FlatTable = 'flat_table';
    case MinEducation = 'min_education';
}
