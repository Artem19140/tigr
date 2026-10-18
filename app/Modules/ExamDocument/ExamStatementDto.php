<?php

namespace App\Modules\ExamDocument;

use Illuminate\Support\Collection;

final readonly class ExamStatementDto
{
    public function __construct(
        public Collection $headers,
        public Collection $rows,
    ){}
}