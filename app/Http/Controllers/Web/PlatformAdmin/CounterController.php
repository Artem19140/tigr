<?php

namespace App\Http\Controllers\Web\PlatformAdmin;

use App\Http\Resources\Counter\CounterResource;
use App\Models\Center;
use App\Models\Counter;
use App\Support\ModelChangesLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

        $request->validate([
            'value' => ['required', 'integer', 'min:1'],
            'password' => ['required', 'string']
        ]);

        $employee  = $request->user();

        $this->checkPasswordAndIfWrongFail(
            $request->input('password'), 
            $employee->password,
            $counter->id
        );

        $counter->value = $request->input('value');
        $counter->save();

        $this->logger->log($counter);

        return response()->noContent();
    }

    protected function checkPasswordAndIfWrongFail(
        string $plain, 
        string $hash,
        int $counterId 
    )
    {  
        $wrongPassword = ! Hash::check($plain, $hash);

        if($wrongPassword){
            Log::info('wrong_password_counter_updating', [
                'counter_id' => $counterId 
            ]);

            abort(404);
        }
    }
}
