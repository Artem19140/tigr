<?php

namespace App\Http\Controllers\Web\Address;

use App\Domain\Center\CenterContext;
use App\Http\Requests\Address\AddressPostRequest;
use App\Http\Resources\Address\AddressResource;
use App\Models\Address;
use App\Models\Center;
use App\Models\Employee;
use App\Support\CenterIsolationCheck;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AddressController
{
    public function index(
        Request $request,
        Center $center
    ): \Inertia\Response {
        //$this->authorize($request->user(), $center);
        $addresses = Address::query()
            ->forCenter(app(CenterContext::class)->id())
            ->withExists('exams as examsExists')
            ->orderByDesc('id')
            ->where('is_active', true)
            ->get();

        Log::info('address_view_index', []);

        CenterIsolationCheck::check($addresses);
        return Inertia::render('Center/Center', [
            'addresses' => AddressResource::collection($addresses),
            'tab' => 'addresses',
            'centerId' => $center->id
        ]);
    }

    public function store(
        AddressPostRequest $request,
        Center $center
    ): JsonResponse {
        //$this->authorize($request->user(), $center);
        $address = Address::create([
            'address' => $request->validated('address'),
            'max_capacity' => $request->validated('capacity'),
            'center_id' => $center->id,
            'creator_id' => $request->user()->id,
        ]);
        return response()->json([
            'address' => new AddressResource($address),
        ],
            201);
    }

    public function update(
        Request $request,
        Center $center,
        Address $address
    ): JsonResponse {
        // $this->authorize($request->user(), $center);
        // abort_if($address->center_id !== $center->id, 404);
        $request->validate([
            'address' => ['required', 'string'],
            'maxCapacity' => ['required', 'integer', 'min:1'],
        ]);
        
        $before = new AddressResource($address)->resolve();
        
        if (! $address->exams()->exists()) {
            $address->address = $request->input('address');
        }

        $address->max_capacity = $request->input('maxCapacity');
        $address->save();
        Log::info('address_updated', [
            'address_id' => $address->id,
            'changes' => [
                'before' => $before,
                'after' => new AddressResource($address)->resolve(),
            ],
        ]);
        return response()->json(new AddressResource($address));
    }

    public function destroy(
        Request $request,
        Center $center,
        Address $address
    ): Response {
        // $this->authorize($request->user(), $center);
        // abort_if($address->center_id !== $center->id, 403);
        $address->is_active = false;
        $address->save();

        Log::info('address_destroy', [
            'address_id' => $address->id,
        ]);
        return response()->noContent();
    }

    protected function authorize(Employee $employee, Center $center): void
    {
        abort_if($employee->center_id !== $center->id, 403);
    }

}
