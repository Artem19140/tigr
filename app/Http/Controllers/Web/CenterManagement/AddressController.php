<?php

namespace App\Http\Controllers\Web\CenterManagement;

use App\Http\Resources\Address\AddressIndexResource;
use App\Http\Resources\Address\AddressResource;
use App\Models\Address;
use App\Support\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Inertia\Inertia;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Patch;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Where;

#[Middleware('auth:web')]
#[Where('address', '[0-9]+')]

class AddressController
{
    public function __construct(
        protected Audit $audit
    ){}

    #[Get('center-manage/addresses', 'addresses.index')]
    #[Authorize('viewAny', Address::class)]
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

    #[Get('addresses/create', 'addresses.create')]
    #[Authorize('create', Address::class)]
    public function create(): \Inertia\Response
    {
        return Inertia::render('CenterManage/AddressCreate', [
            'backUrl' => route('addresses.index', [], false),
            'storeUrl' => route('addresses.store', [], false),
        ]);
    }

    #[Post('addresses', 'addresses.store')]
    #[Authorize('create', Address::class)]
    public function store(
        Request $request
    ): RedirectResponse {

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

    #[Get('center-manage/addresses/{address}/edit', 'addresses.edit')]
    #[Authorize('update', 'address')]
    public function edit(
        Address $address
    ): \Inertia\Response {
        $address->loadExists(['exams as examsExists']);
        return Inertia::render('CenterManage/AddressEdit', [
            'address' => new AddressResource($address),
            'updateUrl' => route('addresses.update', ['address' => $address], false),
            'backUrl' => route('addresses.index', [], false)
        ]);
    }
    
    #[Patch('center-manage/addresses/{address}', 'addresses.update')]
    #[Authorize('update', 'address')]
    public function update(
        Request $request,
        Address $address
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

        $this->audit->log(
            'update',
            $address
        );

        return redirect()->route('addresses.index');
    }

    #[Delete('addresses/{address}', 'addresses.destroy')]
    #[Authorize('delete', 'address')]
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