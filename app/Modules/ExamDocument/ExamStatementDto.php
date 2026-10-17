<?php

namespace App\Modules\ExamDocument;

use App\Models\Exam;

final readonly class ExamStatementDto
{
    public function __construct(
        public Exam $exam,
        public array $headers,
        public array $rows,
    ){}
}