<?php

namespace App\Http\Resources\Block;

use App\Http\Resources\Subblock\SubblockIndexResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockResource extends JsonResource
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
            'name' => $this->resource->name,
            'minMark' => $this->resource->min_mark,
            'subblocks' => SubblockIndexResource::collection($this->subblocks),
            'backUrl' => route('exams-types.index')
        ];
    }
}
