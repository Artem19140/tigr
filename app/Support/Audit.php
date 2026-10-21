<?php

namespace App\Support;

use App\Models\ForeignNational;
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
        if($this->isAdmin()){
            return ;
        }
        try{
            Log::channel('audit')
                ->info($action, [
                    ...$this->getSubjectInfo($subject),
                    ...$context
                ]);
        }  catch (\Throwable $e) {
            report($e);
        }
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

    protected function isAdmin():bool
    {
        $user = auth()->user();

        if(! $user){
            return false;
        }

        if($user instanceof ForeignNational){
            return false;
        }

        return auth()->user()?->isPlatformAdmin();
    }
}