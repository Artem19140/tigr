<?php

namespace App\Http\Dto;

use Carbon\Carbon;

class MinistryEducationDto
{
    public function __construct(
        Carbon $dateFrom,
        Carbon $dateTo,
        bool $lastWeek
    ){}
}