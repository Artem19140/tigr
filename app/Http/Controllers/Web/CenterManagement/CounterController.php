<?php

namespace App\Http\Controllers\Web\CenterManagement;

use App\Http\Resources\Counter\CounterResource;
use App\Models\Counter;
use App\Support\Audit;
use App\Support\ModelAuditChangesBuilder;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Patch;
use Spatie\RouteAttributes\Attributes\Where;

#[Middleware('auth:web')]
#[Where('counter', '[0-9]+')]

class CounterController
{
    public function __construct(
        protected Audit $audit,
        protected ModelAuditChangesBuilder $builder
    ){}
    
    #[Get('center-manage/counters', 'counters.index')]
    #[Authorize('viewAny', Counter::class)]
    public function index(): \Inertia\Response
    {
        return Inertia::render('CenterManage/Counters', [
            'counters' => CounterResource::collection(Counter::all()->sortBy('id'))
        ]);
    }

    #[Patch('counters/{counter}', 'counters.update')]
    #[Authorize('update', Counter::class)]
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

        $this->audit->log(
            'update',
            $counter,
            $this->builder->build(
                $counter
            )
        );

        return redirect()->route('counters.index');
    }
}
