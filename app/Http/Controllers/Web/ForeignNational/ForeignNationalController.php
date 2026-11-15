<?php

namespace App\Http\Controllers\Web\ForeignNational;

use App\Http\Resources\ForeignNational\ForeignNationalEditResource;
use App\Models\Enrollment;
use App\Models\ForeignNationalDocument;
use App\Modules\ForeignNational\CreateForeignNationalWithEnrollment;
use App\Modules\ForeignNational\UpdateForeignNational;
use App\Modules\ForeignNational\ForeignNationalViewBuilder;
use App\Modules\ForeignNational\GetForeignNationals;
use App\Http\Requests\ForeignNational\ForeignNationalIndexRequest;
use App\Http\Requests\ForeignNational\ForeignNationalPostRequest;
use App\Http\Requests\ForeignNational\ForeignNationalUpdateRequest;
use App\Http\Resources\ForeignNational\ForeignNationalIndexResource;
use App\Http\Resources\ForeignNational\ForeignNationalViewResource;
use App\Models\ForeignNational;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Put;
use Spatie\RouteAttributes\Attributes\Where;

#[Middleware('auth:web')]
#[Where('foreign_national', '[0-9]+')]

class ForeignNationalController
{
    #[Get('foreign-nationals', 'foreign-nationals.index')]
    #[Authorize('viewAny', ForeignNational::class)]
    public function index(
        ForeignNationalIndexRequest $request,
        GetForeignNationals $getForeignNationals
    ): Response 
    {
        $dto = $request->toDto();
        $foreignNationals = $getForeignNationals->execute($dto)
            ->simplePaginate($dto->perPage ?? 10)
            ->withQueryString();

        Inertia::flash([ 'filters' => $dto->toFilters()]);

        $employee = $request->user();

        return Inertia::render('ForeignNationals/Index', [
            'foreignNationals' => ForeignNationalIndexResource::collection($foreignNationals),
            'createUrl' =>  $employee->can('create', ForeignNational::class)
                ? route('foreign-nationals.create', [], false)
                : null,
            'indexUrl' => route('foreign-nationals.index', [], false)
        ]);
    }

    #[Get('foreign-nationals/create', 'foreign-nationals.create')]
    #[Authorize('create', ForeignNational::class)]
    public function create(): Response 
    {
        $indexUrl = route('foreign-nationals.index', [], false);
        return Inertia::render('ForeignNationals/Create', [
            'backUrl' => url()->previous($indexUrl),
            'storeUrl' => route('foreign-nationals.store', [], false),
            'documentConstraints' => [
                'mimes' => ForeignNationalDocument::allowedMimes(),
                'maxSizeMb' => ForeignNationalDocument::MAX_SIZE_MB
            ],
            'findMatchesUrl' => route('foreign-nationals.matches', [], false)
        ]);
    }

    #[Post('foreign-nationals', 'foreign-nationals.store')]
    #[Authorize('create', ForeignNational::class)]
    public function store(
        ForeignNationalPostRequest $request,
        CreateForeignNationalWithEnrollment $createForeignNationalWithEnrollment
    ) : JsonResponse {

        Gate::authorize('create', ForeignNational::class);
        
        $enrollement = $createForeignNationalWithEnrollment
            ->execute(
                $request->toDto(),
                $request->validated('examId'),
                $request->user(),
                $request->validated('hasPayment'),
            );

        return response()->json([
            // 'redirectUrl' => route('enrollments.full-documents', [
            //     'enrollment' => $enrollement
            // ])
            'redirectUrl' => route('foreign-nationals.show', [
                'foreign_national' => $enrollement->foreign_national_id,
                'redirectUrl' => route('enrollments.full-documents', [
                    'enrollment' => $enrollement
                ])
            ])
        ]);
    }

    #[Get('foreign-nationals/{foreign_national}',  'foreign-nationals.show')]
    #[Authorize('view', 'foreign_national')]
    public function show(
        Request $request,
        ForeignNational $foreignNational,
        ForeignNationalViewBuilder $builder
    ): Response {

        $buildedForeignNational = $builder->build(
            $foreignNational, 
            $request->user()
        );

        return Inertia::render('ForeignNationals/View', [
            'foreignNational' => new ForeignNationalViewResource($buildedForeignNational),
            'editUrl' => $request->user()->can('update', $foreignNational) 
                ? route('foreign-nationals.edit', [
                    'foreign_national' => $foreignNational
                ], false) 
                : null,

            'enrollUrl' =>  $request->user()->can('create', Enrollment::class) 
                ? route('enrollments.store', [], false)
                : null, 
            'documentConstraints' => [
                'mimes' => ForeignNationalDocument::allowedMimes(),
                'maxSizeMb' => ForeignNationalDocument::MAX_SIZE_MB
            ],
            'redirectUrl' => $request->input('redirectUrl')
        ]);
    }

    #[Get('foreign-nationals/{foreign_national}/edit', 'foreign-nationals.edit')]
    #[Authorize('update', 'foreign_national')]
    public function edit(
        ForeignNational $foreignNational
    ): Response 
    {
        return Inertia::render('ForeignNationals/Edit', [
            'foreignNational' => new ForeignNationalEditResource($foreignNational),
            'updateUrl' => route('foreign-nationals.update', [
                'foreign_national' => $foreignNational
            ], false), 
            'backUrl' => route('foreign-nationals.show', [
                'foreign_national' => $foreignNational
            ], false) 
        ]);
    }

    
    #[Put('foreign-nationals/{foreign_national}', 'foreign-nationals.update')]
    #[Authorize('update', 'foreign_national')]
    public function update(
        ForeignNationalUpdateRequest $request,
        ForeignNational $foreignNational,
        UpdateForeignNational $updateForeignNational
    ) : RedirectResponse 
    {
        Gate::authorize('update', $foreignNational);
        
        $updateForeignNational->execute(
            $request->toDto(),
            $foreignNational
        );

        return response()->redirectTo(route('foreign-nationals.show', [
            'foreign_national' => $foreignNational 
        ]));
    }
}