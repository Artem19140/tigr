<?php

namespace App\Http\Controllers\Web\CenterManagement;

use App\Http\Resources\Center\CenterResource;
use App\Models\Center;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CenterController
{
    public function show(): \Inertia\Response
    {
        $center = Center::firstOrFail();
        return Inertia::render('CenterManage/CenterShow', [
            'center' => new CenterResource($center),
            'editUrl' => route('centers.edit', [], false)
        ]);
    }

    public function edit(): \Inertia\Response
    {
        $center = Center::firstOrFail();
        return Inertia::render('CenterManage/CenterEdit', [
            'center' => new CenterResource($center),
            'backUrl' => route('centers.show', [], false),
            'updateUrl' => route('centers.update', [
                'center' => $center
            ], false),
        ]);
    }

    public function update(
        Request $request, 
        Center $center
    ): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string'],
            'ogrn' => ['required', 'string'],
            'inn' => ['required', 'string'],
            'address' => ['required', 'string'],
            'certificatesIssueAddress' => ['required', 'string'],
            'directorFio' => ['required', 'string'],
            'commissionChairman' => ['required', 'string'],
            'nameGenitive' => ['required', 'string']
        ]);
    
        $center->update([
            'name' => $request->input('name'),
            'ogrn' => $request->input('ogrn'),
            'inn' => $request->input('inn'),
            'address' => $request->input('address'),
            'certificates_issue_address' => $request->input('certificatesIssueAddress'),
            'director_fio' => $request->input('directorFio'),
            'commission_chairman' => $request->input('commissionChairman'),
            'name_genitive' => $request->input('nameGenitive')
        ]);

        return redirect()->route('centers.show', ['center' => $center]);
    }
}
