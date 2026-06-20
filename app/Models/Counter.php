<?php

namespace App\Models;

use App\Enums\CounterKey;
use App\Exceptions\Counter\CounterNotFoundException;
use App\Models\Scopes\BelongsToCenter;
use App\Modules\Center\CenterContext;
use App\Support\CenterIsolationCheck;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    use BelongsToCenter;

    protected $fillable = [
        'key',
        'value',
        'center_id',
        'last_increment_at'
    ];

    protected $casts = [
        'key' => CounterKey::class,
        'last_increment_at' => 'datetime'
    ];

    public static function findLockedOrFail(CounterKey $key, int $centerId): self
    {
        $counter = self::query()
            ->lockForUpdate()
            ->forCenter($centerId)
            ->where('key', $key)
            ->first();

        if(! $counter){
            throw new CounterNotFoundException($key);
        }

        CenterIsolationCheck::centerBelongs($counter, app(CenterContext::class)->id());
        
        return $counter;
    }

    public function reset():void
    {
        $this->value = $this->key->defaultValue();
    }

    public function incrementValue()
    { 
        $this->last_increment_at = Carbon::now();
        $this->value += 1;
    }

    public function notInitialized(): bool
    {
        return $this->last_increment_at === null;
    }

    public function initialize(): void
    {
        $this->last_increment_at = Carbon::now();
    }
}
