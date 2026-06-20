<?php

namespace App\Http\Controllers\Web\ForeignNational;

use App\Modules\ForeignNational\CreateForeignNationalWithEnrollment;
use App\Modules\ForeignNational\UpdateForeignNational;
use App\Modules\ForeignNational\ForeignNationalViewBuilder;
use App\Modules\ForeignNational\GetForeignNationals;
use App\Http\Requests\ForeignNational\ForeignNationalIndexRequest;
use App\Http\Requests\ForeignNational\ForeignNationalPostRequest;
use App\Http\Requests\ForeignNational\ForeignNationalUpdateRequest;
use App\Http\Resources\ForeignNational\ForeignNationalIndexResource;
use App\Http\Resources\ForeignNational\ForeignNationalProfileResource;
use App\Models\ForeignNational;
use App\Support\CenterIsolationCheck;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ForeignNationalController
{
    public function index(
        ForeignNationalIndexRequest $request,
        GetForeignNationals $getForeignNationals
    ): Response {

        Gate::authorize('viewAny', ForeignNational::class);
        $dto = $request->toDto();
        $foreignNationals = $getForeignNationals->execute($dto);

        Inertia::flash(['filters' => $request->validated(), 'test' => $dto->toFilters()]);

        $employee = $request->user();
        
        CenterIsolationCheck::check($foreignNationals);

        return Inertia::render('ForeignNationals/ForeignNationals', [
            'foreignNationals' => ForeignNationalIndexResource::collection($foreignNationals),
            'permissions' => [
                'create' => $employee->can('create', ForeignNational::class),
                'export' => $employee->can('export', ForeignNational::class),
                'statistics' => $employee->can('statistics'),
                'ministryEducation' => $employee->can('reports.min-education'),
            ],
        ]);
    }

    public function store(
        ForeignNationalPostRequest $request,
        CreateForeignNationalWithEnrollment $createForeignNationalWithEnrollment
    ): JsonResponse {
        Gate::authorize('create', ForeignNational::class);
        $enrollement = $createForeignNationalWithEnrollment
            ->execute(
                $request->toDto(),
                $request->validated('examId'),
                $request->user(),
                $request->validated('hasPayment'),
            );

        return response()->json([
            'redirectUrl' => route('enrollments.statements', [
                'enrollment' => $enrollement
            ]),
        ]);
    }

    public function show(
        Request $request,
        ForeignNational $foreignNational,
        ForeignNationalViewBuilder $builder
    ): JsonResponse {
        Gate::authorize('view', $foreignNational);

        $buildedForeignNational = $builder->build(
            $foreignNational, 
            $request->user()
        );
        
        return response()->json([
            'foreignNational' => new ForeignNationalProfileResource($buildedForeignNational),
        ]);
    }

    public function update(
        ForeignNationalUpdateRequest $request,
        ForeignNational $foreignNational,
        UpdateForeignNational $updateForeignNational
    ): JsonResponse {
        $updatedForeignNational = $updateForeignNational->execute(
            $request->toDto(),
            $foreignNational
        );

        return response()->json([
            'foreignNational' => new ForeignNationalProfileResource($updatedForeignNational),
        ]);
    }
}