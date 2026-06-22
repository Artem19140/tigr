<?php

namespace App\Http\Dto;

use Carbon\Carbon;

final readonly class ExamDto
{
    public function __construct(
        public Carbon $beginTime,
        public int $addressId,
        public int $examTypeId,
        public string $comment,
        public array $examiners,
        public int $capacity
    ) {}

    public function toArray(): array 
    {
        return [
            'begin_time' => $this->beginTime,
            'address_id' => $this->addressId,
            'exam_type_id' => $this->examTypeId,
            'comment' => $this->comment,
            'capacity' => $this->capacity
        ];
    }
}
