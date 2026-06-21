<?php

namespace App\Listeners;

use App\Events\ExamDocumentGenerated;
use App\Support\Audit;

class LogExamDocumentGenerated
{
    public function __construct(protected Audit $audit)
    {
        //
    }

    public function handle(ExamDocumentGenerated $event): void
    {
        $this->audit->log(
            'document_generated',
            $event->exam, 
            [
                'document' => $event->type->value,
                'context' => $event->context
            ]
        );
    }
}
