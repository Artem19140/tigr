<?php

namespace App\Http\Controllers\Web\ForeignNational;

use App\Http\Requests\ForeignNational\ForeignNationalIndexRequest;
use App\Modules\ForeignNational\GetForeignNationals;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Spatie\RouteAttributes\Attributes\Get;

#[Middleware('auth:web')]
#[Authorize('findMatches', ForeignNational::class)]

class MatchesController
{
    #[Get('foreign-nationals/matches', 'foreign-nationals.matches')]    
    public function index(
        ForeignNationalIndexRequest $request,
        GetForeignNationals $getForeignNationals
    ): JsonResponse {
        $matches = $getForeignNationals->execute(
            $request->toDto()
        )
            ->limit(10)
            ->get()
            ->map(function($match){
                return [
                    'id' => $match->id,
                    'fullName' => $match->full_name,
                    'passport'=> $match->formattedDateBirth(),
                    'showUrl' => route('foreign-nationals.show', [
                        'foreign_national' => $match->id
                    ], false)
                ];
            });

        return response()->json([
            'matches' => $matches
        ]);
    }
    
}
