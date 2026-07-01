<?php

namespace App\Http\Controllers\Web\CenterManage;

use App\Enums\CounterKey;
use App\Http\Requests\Center\CenterStoreRequest;
use App\Http\Requests\Center\CenterUpdateRequest;
use App\Http\Resources\Center\CenterIndexResource;
use App\Http\Resources\Center\CenterResource;
use App\Models\Center;
use App\Models\Counter;
use App\Support\ModelChangesLogger;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CenterController
{
    public function __construct(
        protected ModelChangesLogger $logger
    ){}

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
    public function show(Center $center): \Inertia\Response
    {
        return Inertia::render('Center/Center', [
            'data' => new CenterResource($center),
            'tab' => 'data',
            'centerId' => $center->id
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

    public function update(
        CenterUpdateRequest $request,
        Center $center
    ): CenterResource {
        $dto = $request->dto();

        $center->update($dto->toArray());

        $this->logger->log($center);

        return new CenterResource($center);
    }

    public function destroy(Center $center): Response
    {
        $center->is_active = false;

        return response()->noContent();
    }
}
