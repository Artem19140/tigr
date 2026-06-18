<?php

namespace App\Http\Controllers\Web\Document;

use App\Modules\Document\DocumentService;
use App\Http\Resources\Document\DocumentResource;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class DocumentController
{
    public function show(
        Request $request,
        Document $document
    )
    {
        Log::info('document_access', [
            'document' => $document->id
        ]);
        return Storage::disk('local')->response($document->path);
    }

    public function update(
        Request $request,
        Document $document,
        DocumentService $documentService
    )
    {
        $request->validate([
            'document' => ['required']
        ]);

        $document->deleted_at = Carbon::now();
        $document->save();
        
        $newDocument = $documentService->create(
            $request->file('document'),
            $document->documentable,
            $document->document_type
        );

        Log::info('document_updated', [
            'old_doc' => $document->id,
            'new_doc' => $newDocument->id
        ]);

        return new DocumentResource($newDocument);
    }
}
