<?php

namespace App\Http\Controllers\Web\CenterManagement;

use App\Http\Resources\Counter\CounterResource;
use App\Models\Counter;
use App\Support\ModelChangesLogger;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CounterController
{
    public function __construct(
        protected ModelChangesLogger $logger
    ){}
    public function index(): \Inertia\Response
    {
        return Inertia::render('CenterManage/Counters', [
            'counters' => CounterResource::collection(Counter::all()->sortBy('id'))
        ]);
    }

    public function update(
        Request $request,
        Counter $counter
    ): RedirectResponse
    { 
        $request->validate([
            'value' => ['required', 'integer', 'min:1', 'regex:' . $counter->regexValidaton()]
        ]);

        if($counter->value === $request->input('value')){
            throw ValidationException::withMessages([
                'value' => 'Новое значение должно отличаться от старого'
            ]);
        }

        $inctementedAt = $counter->notInitialized() 
            ? null
            : Carbon::now();

        $counter->update([
            'value' => $request->input('value'),
            'last_increment_at' => $inctementedAt
        ]);

        $this->logger->log($counter);

        return redirect()->route('counters.index');
    }
}
