<?php

namespace App\Http\Resources\ExamType;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class ExamTypeResource extends JsonResource
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
            'amount' => $this->amount,
            'amountInWords' => Number::spell($this->amount, 'ru'),
            'minMark' => $this->min_mark,
            'tasksCount' => $this->tasks_count,
            'blocksUrl' => route('blocks.index', [
                'exam_type' => $this->resource
            ])
        ];
    }
}
