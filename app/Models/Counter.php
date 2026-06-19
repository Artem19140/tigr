<?php

namespace App\Models;

use App\Enums\CounterKey;
use App\Exceptions\Counter\CounterNotFoundException;
use App\Models\Scopes\BelongsToCenter;
use App\Modules\Center\CenterContext;
use App\Support\CenterIsolationCheck;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    use BelongsToCenter;

    protected $fillable = [
        'key',
        'value',
        'center_id',
        'last_incremented_at'
    ];

    protected $casts = [
        'key' => CounterKey::class,
        'last_incremented_at' => 'datetime'
    ];

    public static function findLockedOrFail(CounterKey $key, int $centerId)
    {
        $counter = self::query()
            ->lockForUpdate()
            ->forCenter($centerId)
            ->where('key', $key)
            ->first();

        CenterIsolationCheck::centerBelongs($counter, app(CenterContext::class)->id());

        if(! $counter){
            throw new CounterNotFoundException($key);
        }

        return $counter;
    }

    public function reset():void
    {
        $this->value = $this->key->defaultValue();
    }
}
