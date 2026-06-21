<?php

namespace App\Listeners;

use App\Events\ReportGenerated;
use App\Support\Audit;

class LogReportGenerated
{
    public function __construct(protected Audit $audit)
    {
        
    }

    public function handle(ReportGenerated $event): void
    {
        $this->audit->log(
            'report_generated',
            $event->type->value, 
            $event->context
        );
    }
}
