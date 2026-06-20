<?php

namespace App\Http\Controllers\Web\Address;

use App\Modules\Center\CenterContext;
use App\Http\Requests\Address\AddressPostRequest;
use App\Http\Resources\Address\AddressResource;
use App\Models\Address;
use App\Models\Center;
use App\Models\Employee;
use App\Support\CenterIsolationCheck;
use App\Support\ModelChangesLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AddressController
{
    public function __construct(
        protected CenterContext $centerContext
    ){}
    public function index(
        Request $request,
        Center $center
    ): \Inertia\Response {
        $this->authorize($request->user(), $center);

        $addresses = Address::query()
            ->forCenter($this->centerContext->id())
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
        $this->authorize($request->user(), $center);

        $address = Address::create([
            'address' => $request->validated('address'),
            'max_capacity' => $request->validated('capacity'),
            'center_id' => $center->id,
            'creator_id' => $request->user()->id,
        ]);

        return response()->json([
            'address' => new AddressResource($address),
        ], 201);
    }

    public function update(
        Request $request,
        Center $center,
        Address $address,
        ModelChangesLogger $logger
    ): JsonResponse {
        $this->authorize($request->user(), $center);
        $this->abortIfNotBelongsCenter($center, $address);

        $request->validate([
            'address' => ['required', 'string'],
            'maxCapacity' => ['required', 'integer', 'min:1'],
        ]);
        
        if (! $address->exams()->exists()) {
            $address->address = $request->input('address');
        }

        $address->max_capacity = $request->input('maxCapacity');
        $address->save();
        $logger->log($address);
        return response()->json(new AddressResource($address));
    }

    public function destroy(
        Request $request,
        Center $center,
        Address $address
    ): Response {
        $this->authorize($request->user(), $center);
        $this->abortIfNotBelongsCenter($center, $address);

        $address->is_active = false;
        $address->save();

        Log::info('address_destroy', [
            'address_id' => $address->id,
        ]);
        return response()->noContent();
    }

    protected function authorize(Employee $employee, Center $center): void
    {
        if($employee->isPlatformAdmin()){
            return ;
        }
        abort_if($employee->center_id !== $center->id, 404);
    }

    protected function abortIfNotBelongsCenter(Center $center, Address $address){
        if(request()->user()->isPlatformAdmin()){
            return ;
        }
        abort_if($address->center_id !== $center->id, 404);
    }
}