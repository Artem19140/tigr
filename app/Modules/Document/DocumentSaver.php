<?php

namespace App\Modules\Document;

use App\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class DocumentSaver{
    public function store(
        UploadedFile $file,
        Model $owner,
        string $doctype
    ): Document
    {
        $path = $file->store('documents');

        $document = $owner->documents()->create([
            'path' => $path,
            'mime_type' =>  $file->getMimeType(),
            'size_bytes' => $file->getSize(),
            'document_type' => $doctype,
            'creator_id' => auth()->user()->id,
            'original_name' => $file->getClientOriginalName(),
            'center_id' => $owner->center_id
        ]);

        return $document;
    }
}