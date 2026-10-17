<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

class ModelAuditChangesBuilder
{
    public function build(
        Model $model, 
        array $relations = []
    ): array {
        $before = $model->getPrevious();
        $after = $model->getChanges();
        unset($before['updated_at'], $after['updated_at']);

        return [
            'before' => $before,
            'after' => $after,
            'relations' => $relations
        ];
    }
}