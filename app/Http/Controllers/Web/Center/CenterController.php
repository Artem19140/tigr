<?php

namespace App\Http\Controllers\Web\Center;

use App\Http\Requests\Center\CenterUpdateRequest;
use App\Http\Resources\Center\CenterResource;
use App\Models\Center;
use App\Support\ModelChangesLogger;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class CenterController
{
    public function __construct(
        protected ModelChangesLogger $logger
    ){}
    public function show(Center $center): Response
    {
        Log::info('center_data_view', ['center_id' => $center->id]);
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

        $center->name = $dto->name;
        $center->director_fio = $dto->directorFio;
        $center->certificates_issue_address = $dto->certificatesIssueAddress;
        $center->ogrn = $dto->ogrn;
        $center->inn = $dto->inn;
        $center->address = $dto->address;
        $center->name_genitive = $dto->nameGenitive;
        $center->commission_chairman = $dto->commissionChairman;

        $center->save();

        $this->logger->log($center);

        return new CenterResource($center);
    }
}
