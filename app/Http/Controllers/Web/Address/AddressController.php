<?php

namespace App\Http\Controllers\Web\Address;

use App\Http\Resources\Address\AddressResource;
use App\Models\Address;
use App\Support\Audit;
use App\Support\ModelChangesLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class AddressController
{
    public function __construct(
        protected Audit $audit
    ){}
    public function index(
        Request $request
    ): \Inertia\Response {
        $addresses = Address::query()
            ->withExists('exams as examsExists')
            ->orderByDesc('id')
            ->where('is_active', true)
            ->get();

        return Inertia::render('Center/Center', [
            'addresses' => AddressResource::collection($addresses),
            'tab' => 'addresses'
        ]);
    }

    public function store(
        Request $request
    ): JsonResponse {

        $request->validate([
            'address' => ['required', 'string'],
            'capacity' => ['required', 'integer', 'min:1'],
        ]);

        $address = Address::create([
            'address' => $request->input('address'),
            'capacity' => $request->input('capacity'),
            'creator_id' => $request->user()->id,
        ]);

        $this->audit->log('create', $address);

        return response()->json([
            'address' => new AddressResource($address),
        ], 201);
    }

    public function update(
        Request $request,
        Address $address,
        ModelChangesLogger $logger
    ): JsonResponse {
        $request->validate([
            'address' => ['required', 'string'],
            'capacity' => ['required', 'integer', 'min:1'],
        ]);
        
        if (! $address->exams()->exists()) {
            $address->address = $request->input('address');
        }

        $address->capacity = $request->input('capacity');
        $address->save();
        $logger->log($address);
        return response()->json(new AddressResource($address));
    }

    public function destroy(
        Address $address
    ): Response {

        $address->is_active = false;
        $address->save();

        $this->audit->log('delete', $address);

        return response()->noContent();
    }
}