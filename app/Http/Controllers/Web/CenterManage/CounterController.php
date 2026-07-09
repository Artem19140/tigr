<?php

namespace App\Http\Controllers\Web\CenterManage;

use App\Http\Resources\Counter\CounterResource;
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
    public function index()
    {
        return Inertia::render('Center/Center', [
            'counters' => CounterResource::collection(Counter::all()),
            'tab' => 'counters'
        ]);
    }

    public function update(
        Request $request,
        Counter $counter
    )
    { 
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
