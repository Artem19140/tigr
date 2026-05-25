<?php

namespace App\Models;

use App\Enums\CounterKey;
use App\Models\Scopes\BelongsToCenter;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    use BelongsToCenter;

    protected $fillable = [
        'key',
        'value',
        'center_id',
    ];

    protected $casts = [
        'key' => CounterKey::class,
    ];
}
