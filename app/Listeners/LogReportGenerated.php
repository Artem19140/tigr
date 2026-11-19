<?php

namespace App\Listeners;

use App\Events\ReportGenerated;

class LogReportGenerated
{
    public function __construct(){}

    public function handle(ReportGenerated $event): void
    {
        activity()
            ->event('report_generated')
            ->log('frdo');
    }
}
