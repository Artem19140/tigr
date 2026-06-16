<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

final class ModelChangesLogger
{
    public function log(Model $model):void
    {
        $modelName = strtolower(class_basename($model));
        $message = "{$modelName}_updated";

        Log::info($message, [
            'model_id' => $model->id,
            'model_type' =>  class_basename($model),
            'changes' => [
                'before' => $model->getPrevious(),
                'after' => $model->getChanges(),
            ]
        ]);
    }
}