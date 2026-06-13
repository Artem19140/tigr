<?php

namespace  App\Http\Resources\Document;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'size' => $this->size_kb,
            'mimeType' => $this->mime_type,
            'createdAt' => $this->created_at->format('d.m.Y'),
            'type' => $this->document_type,
            'creatorFullName' => $this->whenLoaded('creator', fn () => $this->creator->full_name),
            'permissions' => [
                'update' => true //$request->user()->can('updateAny', Document::class)
            ],
            
        ];
    }
}
