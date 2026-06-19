<?php

namespace App\Http\Controllers\Web\PlatformAdmin;

use App\Enums\CounterKey;
use App\Http\Requests\Center\CenterStoreRequest;
use App\Http\Resources\Center\CenterIndexResource;
use App\Models\Center;
use App\Models\Counter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CenterPlatformAdminController
{
    public function index(): \Inertia\Response
    {
        $centers = Center::query()
            ->withCount('employees')
            ->orderBy('id')
            ->get();

        return Inertia::render('PlatformAdmin/Centers', [
            'centers' => CenterIndexResource::collection($centers),
        ]);
    }

    public function store(CenterStoreRequest $request): Response
    {
        $wrongPassword = ! Hash::check(
            $request->validated('password'),
            $request->user()->password
        );

        if ($wrongPassword) {
            Log::warning('wrong platform admin password, center creating');
            abort(404);
        }

        DB::transaction(function() use($request){
            $center = Center::create([
                'short_name' => $request->validated('shortName'),
                'time_zone' => $request->validated('timeZone'),
            ]);

            $counters = CounterKey::cases();

            foreach($counters as $counter){
                Counter::create([
                    'center_id' => $center->id,
                    'key' => $counter,
                    'value' => $counter->defaultValue()
                ]);
            }
        });

        return response()->noContent();
    }

    public function destroy(Center $center): Response
    {
        $center->is_active = false;

        return response()->noContent();
    }

    public function change(Request $request){
        $request->validate([
            'centerId' => ['required', 'integer', 'min:1', 'exists:centers,id']
        ]);
        session()->put('center_id', $request->input('centerId'));
        return response()->noContent();
    }

    public function reset(){
        session()->forget('center_id');
        return response()->noContent();
    }
}
