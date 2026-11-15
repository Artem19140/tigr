<?php

namespace App\Http\Controllers\Web\CenterManagement;

use App\Http\Resources\Center\CenterResource;
use App\Models\Center;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Put;

#[Middleware('auth:web')]

class CenterController
{
    #[Get('center-manage', 'centers.show')]
    #[Authorize('view', Center::class)]
    public function show(): \Inertia\Response
    {
        $center = Center::firstOrFail();
        return Inertia::render('CenterManage/CenterShow', [
            'center' => new CenterResource($center),
            'editUrl' => route('centers.edit', [], false)
        ]);
    }

    #[Get('center-manage/edit', 'centers.edit')]
    #[Authorize('edit', Center::class)]
    public function edit(): \Inertia\Response
    {
        $center = Center::firstOrFail();
        return Inertia::render('CenterManage/CenterEdit', [
            'center' => new CenterResource($center),
            'backUrl' => route('centers.show', [], false),
            'updateUrl' => route('centers.update', [
                'center' => $center
            ], false),
            'timeZones' => [
                'Europe/Moscow',
                'Europe/Samara'
            ]
        ]);
    }

    #[Put('centers/{center}', 'centers.update')]
    #[Authorize('edit', Center::class)]
    public function update(
        Request $request, 
        Center $center
    ): RedirectResponse {
        $request->validate([
            'name' => ['required', 'string'],
            'shortName' => ['required', 'string'],
            'ogrn' => ['required', 'string'],
            'inn' => ['required', 'string'],
            'registeredAddress' => ['required', 'string'],
            'certificatesIssueAddress' => ['required', 'string'],
            'directorFio' => ['required', 'string'],
            'commissionChairman' => ['required', 'string'],
            'nameGenitive' => ['required', 'string'],
            'timeZone' => ['required', 'string', 'in:Europe/Moscow,Europe/Samara']
        ]);
    
        $center->update([
            'name' => $request->input('name'),
            'short_name' => $request->input('shortName'),
            'ogrn' => $request->input('ogrn'),
            'inn' => $request->input('inn'),
            'registered_address' => $request->input('registeredAddress'),
            'certificates_issue_address' => $request->input('certificatesIssueAddress'),
            'director_fio' => $request->input('directorFio'),
            'commission_chairman' => $request->input('commissionChairman'),
            'name_genitive' => $request->input('nameGenitive'),
            'time_zone' => $request->input('timeZone')
        ]);
        
        Cache::forget(Center::CACHE_KEY);

        return redirect()->route('centers.show');
    }
}
