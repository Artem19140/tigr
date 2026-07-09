<?php

namespace App\Http\Controllers\Web\Document;

use App\Modules\Document\DocumentSaver;
use App\Http\Resources\Document\DocumentResource;
use App\Models\Document;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class DocumentController
{
    public function __construct(
        protected Audit $audit
    ){}
    public function show(
        Document $document
    )
    {
        Gate::authorize('view',$document);
        $this->audit->log('view', $document);

        return Storage::disk('local')->response($document->path);
    }

    public function update(
        Request $request,
        Document $document,
        DocumentSaver $documentSaver
    )
    {
        $request->validate([
            'document' => ['required', 'file']
        ]);

        $document->deleted_at = Carbon::now();
        $document->save();
        
        $newDocument = $documentSaver->store(
            $request->file('document'),
            $document->documentable,
            $document->document_type
        );

        $this->audit->log(
            'document_delete',
            $document, 
            ['new_doc_id' => $newDocument->id]
        );

        $this->audit->log(
            'create', 
            $newDocument, 
            ['old_doc_id' => $document->id]
        );

        return new DocumentResource($newDocument);
    }
}
