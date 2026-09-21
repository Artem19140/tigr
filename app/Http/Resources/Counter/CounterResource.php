<?php

namespace App\Http\Resources\Counter;

use App\Enums\CounterKey;
use App\Exceptions\Counter\CounterNotFoundException;
use App\Modules\Counter\GroupNumberGenerator;
use App\Modules\Counter\RegNumberGenerator;
use App\Modules\Counter\SessionNumberGenerator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CounterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'key' => $this->resource->key,
            'value' => $this->resource->value,
            'nextValue' => $this->nextValue($this->resource->key),
            'updateUrl' => $request->user()->can('update', $this->resource)
                ? route('counters.update', ['counter' => $this->resource], false)
                : null
        ];
    }

    protected function nextValue(CounterKey $key): int
    {
        return match($key){
            CounterKey::RegNum => new RegNumberGenerator()->execute(true),
            CounterKey::Group => new GroupNumberGenerator()->execute(true),
            CounterKey::Session => new SessionNumberGenerator()->execute(true),
            default => throw new CounterNotFoundException($key)
        }; 
    }
}
