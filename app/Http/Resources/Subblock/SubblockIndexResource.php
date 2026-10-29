<?php

namespace App\Http\Resources\Subblock;

use App\Http\Resources\Task\TaskResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubblockIndexResource extends JsonResource
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
            'name' => $this->resource->name,
            'minMark' => $this->resource->min_mark,
            'tasks' => TaskResource::collection($this->whenLoaded('tasks'))
        ];
    }
}
