<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Audit
{
    public static function log(
        string $action,
        Model | string $subject,
        array $context = []
    )
    {
        Log::channel('audit')
            ->info($action, [
                ...self::getSubjectInfo($subject),
                ...$context
            ]);
    }

    protected static function getSubjectInfo(Model | string $subject): array
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