<?php

namespace App\Http\Controllers\Web\ForeignNational;

use App\Models\ForeignNationalDocument;
use App\Support\Audit;
use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Validation\Rules\File;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;
use Spatie\RouteAttributes\Attributes\Where;

#[Middleware('auth:web')]
#[Where('foreign_national_document', '[0-9]+')]
#[Prefix('foreign-nationals/documents')]

class ForeignNationalDocumentsController
{
    #[Get('{foreign_national_document}', 'foreign-nationals.documents.show')]
    #[Authorize('view', 'foreign_national_document')]
    public function show(
        ForeignNationalDocument $foreignNationalDocument,
        Audit $audit
    )
    {
        $audit->log('view', $foreignNationalDocument);

        return Storage::disk($foreignNationalDocument->disk)
            ->response($foreignNationalDocument->path);
    }

    #[Put('{foreign_national_document}', 'foreign-nationals.documents.update')]
    #[Authorize('update', 'foreign_national_document')]
    public function update(
        Request $request, 
        ForeignNationalDocument $foreignNationalDocument,
        Audit $audit
    ): RedirectResponse
    {
        $allowedMimes = ForeignNationalDocument::allowedMimes();
        $allowedMimesImploded = implode(',', $allowedMimes);
        
        $maxSizeMb = ForeignNationalDocument::MAX_SIZE_MB;
        
        $request->validate([
            'document' => [
                'required',
                'file',
                "mimes:$allowedMimesImploded",
                File::types($allowedMimes)
                    ->max("{$maxSizeMb}mb"),
            ]
        ]);

        DB::transaction(function() use(
            $request, 
            $foreignNationalDocument, 
            $audit
        ){
            $foreignNationalDocument->update([
                'deleted_at' => Carbon::now()
            ]);

            $document = $request->file('document');

            $newDocument = ForeignNationalDocument::create(
                [
                    'foreign_national_id' => $foreignNationalDocument->foreign_national_id,
                    'path' => $document->store('documents'),
                    'mime_type' =>  $document->getMimeType(),
                    'size_bytes' => $document->getSize(),
                    'document_type' => $foreignNationalDocument->document_type,
                    'creator_id' => auth()->user()->id,
                    'original_name' => $document->getClientOriginalName(),
                    'disk' => config('filesystems.default')
                ]
            );

            $audit->log(
                'document_delete',
                $foreignNationalDocument, 
                ['new_doc_id' => $newDocument->id]
            );

            $audit->log(
                'create', 
                $newDocument, 
                ['old_doc_id' => $foreignNationalDocument->id]
            );

            Inertia::flash([
                'success' => 'Документ успешно обновлен'
            ]);
        });
        return redirect()->route('foreign-nationals.show', [
            'foreign_national' => $foreignNationalDocument->foreign_national_id,
        ]);
    }
}
