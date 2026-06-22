<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

final class ModelChangesLogger
{
    public function __construct(
        protected Audit $audit
    ){}
    public function log(Model $model, array $relations = []):void
    {
        $before = $model->getPrevious();
        $after = $model->getChanges();
        unset($before['updated_at'], $after['updated_at']);

        $this->audit->log(
            'updated', 
            $model,
            [
                'changes' => [
                    'before' => $before,
                    'after' => $after,
                    'relations' => $relations
                ]
            ]
        );
    }
}