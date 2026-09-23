<?php

namespace App\Http\Resources\ForeignNational;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForeignNationalDocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'updatedAt' => $this->updated_at->format('d.m.Y'),
            'actions' => [
                'downloadUrl' => $request->user()->can('view', $this->resource)
                    ? route('foreign-nationals.documents.show', [
                        'foreign_national_document' => $this->resource,
                    ]) 
                    : null,
                'updateUrl' => $request->user()->can('update', $this->resource)
                    ? route('foreign-nationals.documents.update', [
                        'foreign_national_document' => $this->resource,
                    ]) 
                    : null,
            ],
            'type' => $this->document_type,
        ];
    }
}
