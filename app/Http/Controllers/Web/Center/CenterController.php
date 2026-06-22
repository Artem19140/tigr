<?php

namespace App\Http\Controllers\Web\Center;

use App\Http\Requests\Center\CenterUpdateRequest;
use App\Http\Resources\Center\CenterResource;
use App\Models\Center;
use App\Support\ModelChangesLogger;
use Inertia\Inertia;
use Inertia\Response;

class CenterController
{
    public function __construct(
        protected ModelChangesLogger $logger
    ){}
    public function show(Center $center): Response
    {
        return Inertia::render('Center/Center', [
            'data' => new CenterResource($center),
            'tab' => 'data',
            'centerId' => $center->id
        ]);

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
}
