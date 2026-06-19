<?php

namespace App\Http\Controllers\Web\PlatformAdmin;

use App\Http\Resources\Counter\CounterResource;
use App\Models\Center;
use App\Models\Counter;
use App\Support\ModelChangesLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CounterController
{
    public function __construct(
        protected ModelChangesLogger $logger
    ){}
    public function index(Center $center)
    {
        $center->load('counters');
        return Inertia::render('Center/Center', [
            'counters' => CounterResource::collection($center->counters),
            'tab' => 'counters',
            'centerId' => $center->id
        ]);
    }

    public function update(
        Request $request, 
        Center $center, 
        Counter $counter
    )
    {
        abort_if(
            $center->id !== $counter->center_id || ! $request->user()->isPlatformAdmin(),
            404
        );
        
        $request->validate([
            'value' => ['required', 'integer', 'min:1']
        ]);

        if($counter->value === $request->input('value')){
            throw ValidationException::withMessages([
                'value' => 'Новое значение должно отличаться от старого'
            ]);
        }

        $counter->value = $request->input('value');
        $counter->save();

        $this->logger->log($counter);

        return response()->noContent();
    }
}
