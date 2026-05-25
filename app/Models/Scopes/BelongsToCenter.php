<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToCenter
{
    protected static function scopeForCenter(
        Builder $query,
        ?int $centerId = null
    ): Builder {
        if (! $centerId) {
            return $query;
        }

        return $query->where(
            $query->qualifyColumn('center_id'),
            $centerId
        );
    }
}
