<?php

namespace App\Listeners;

use App\Events\ExamDocumentGenerated;

class LogExamDocumentGenerated
{
    public function __construct()
    {
        //
    }

    public function handle(ExamDocumentGenerated $event): void
    {
        activity()
            ->performedOn($event->exam)
            ->event('document_generated')
            ->withProperties($event->context)
            ->log($event->type->value);
    }
}
