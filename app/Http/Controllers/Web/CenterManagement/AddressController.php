<?php

namespace App\Http\Controllers\Web\CenterManagement;

use App\Http\Resources\Address\AddressIndexResource;
use App\Models\Address;
use App\Support\Audit;
use App\Support\ModelChangesLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->get();

        return Inertia::render('CenterManage/Addresses', [
            'addresses' => AddressIndexResource::collection($addresses),
            'createUrl' => $request->user()->can('create', Address::class)
                ? route('addresses.create', [], false)
                : null
        ]);
    }
    public function create()
    {
        return Inertia::render('CenterManage/AddressCreate', [
            'backUrl' => route('addresses.index', [], false),
            'storeUrl' => route('addresses.store', [], false),
        ]);
    }

    public function store(
        Request $request
    ) {

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

        return redirect()->route('addresses.index');
    }

    public function update(
        Request $request,
        Address $address,
        ModelChangesLogger $logger
    ): RedirectResponse {
        $request->validate([
            'address' => ['required', 'string'],
            'capacity' => ['required', 'integer', 'min:1'],
        ]);
        
        if (! $address->exams()->exists()) {
            $address->address = $request->input('address');
        }

        $address->capacity = $request->input('capacity');

        $address->update([
            'capacity' => $request->input('capacity')
        ]);

        $logger->log($address);
        return redirect()->route('addresses.index');
    }

    public function destroy(
        Address $address
    ): RedirectResponse {

        $address->update([
            'is_active' => false
        ]);

        $this->audit->log('delete', $address);

        return redirect()->route('addresses.index');
    }
}