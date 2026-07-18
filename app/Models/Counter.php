<?php

namespace App\Models;

use App\Enums\CounterKey;
use App\Exceptions\Counter\CounterNotFoundException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    protected $fillable = [
        'key',
        'value',
        'last_increment_at'
    ];

    protected $casts = [
        'key' => CounterKey::class,
        'last_increment_at' => 'datetime'
    ];

    public static function findLockedOrFail(CounterKey $key): self
    {
        $counter = self::query()
            ->lockForUpdate()
            ->where('key', $key)
            ->first();

        if(! $counter){
            throw new CounterNotFoundException($key);
        }
        
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
