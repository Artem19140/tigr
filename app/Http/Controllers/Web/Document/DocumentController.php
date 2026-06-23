<?php

namespace App\Http\Controllers\Web\Document;

use App\Exceptions\BusinessException;
use App\Models\Exam;
use App\Models\Upload;
use App\Modules\Document\DocumentSaver;
use App\Http\Resources\Document\DocumentResource;
use App\Models\Document;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
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

    public function link(Request $request)
    {
        $request->validate([
            'type' => ['required', 'string', 'in:video'],
            'documentableType' => ['required', 'string', 'in:exam'],
            'documentableId' => ['required', 'integer', 'min:1'],
            'uploadId' => ['required', 'integer', 'min:1']
        ]);

        $exam = $this->getModel($request->input('documentableType'))::findOrFail($request->input('documentableId')); // Находить по модели
        $upload = Upload::findOrFail($request->input('uploadId'));

        $doc = $exam->documents()->create([
            'creator_id' => $request->user()->id,
            'original_name' => $upload->original_name,
            'size_bytes' => 123,
            'mime_type' => $upload->mime_type,
            'document_type' => $request->input('type'),
            'center_id' => $request->user()->center_id,
            'path' => $upload->path
        ]);
        return response()->json([
            'document' => new DocumentResource($doc)
        ]);
    }

    protected function getModel(string $documentable):string
    {
        return match($documentable){
            'exam' => Exam::class
        };
    }

    protected function documentValidator(
        string $documentable,
        string $mime
    ):void
    {
        $allowedMimes = $this->allowedMimes($documentable);
        $mimeNotAllowed = ! \in_array($mime, $allowedMimes);

        if(! $mimeNotAllowed){
            Log::warning('mime not allowed', [
                'mime' => $mime,
                'documentable' => $documentable
            ]);
            throw new BusinessException('Неверный тип файла для exam');
        }
    }

    protected function allowedMimes(string $documentable): array
    {
        $whiteListMimes = [
            'exam' => [
                'video/mp4',
                'video/webm',
                'video/ogg',
                'video/mpeg'
            ],
            'foreign-national' => [
                'application/pdf'
            ]
        ];
        //проверка, что есть в списке и лог если нет
        return $whiteListMimes[$documentable];
    }
}
