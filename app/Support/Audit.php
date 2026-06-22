<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Audit
{
    public function log(
        string $action,
        Model | string $subject,
        array $context = []
    ): void
    {
        Log::channel('audit')
            ->info($action, [
                ...$this->getSubjectInfo($subject),
                ...$context
            ]);
    }

    protected function getSubjectInfo(Model | string $subject): array
    {
        if($subject instanceof Model){
            return [
                'subject_type' =>  Str::snake(class_basename($subject)),
                'subject_id' => $subject->id,
            ] ;
        }

        return ['subject_type' => $subject];
    }
}