<?php

namespace App\Http\Controllers\Web\ForeignNational;

use App\Models\ForeignNationalDocument;
use App\Support\Audit;
use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Storage;

class ForeignNationalDocumentsController
{
    public function show(
        ForeignNationalDocument $foreignNationalDocument,
        Audit $audit
    )
    {
        $audit->log('view', $foreignNationalDocument);

        return Storage::disk('local')->response($foreignNationalDocument->path);
    }

    public function update(
        Request $request, 
        ForeignNationalDocument $foreignNationalDocument,
        Audit $audit
    ): RedirectResponse
    {
        $request->validate([
            'document' => ['required', 'file']
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
                    'original_name' => $document->getClientOriginalName()
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
