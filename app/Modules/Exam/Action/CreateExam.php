<?php

namespace App\Modules\Exam\Action;

use App\Modules\Exam\Validator\ExamBeforeSaveValidator;
use App\Http\Dto\ExamDto;
use App\Models\Employee;
use App\Models\Exam;
use DB;

final class CreateExam
{
    public function __construct(
        protected ExamBeforeSaveValidator $examBeforeSaveValidator
    ) {}

    public function execute(
        ExamDto $examDto,
        Employee $employee
    ): Exam {
        $duration = $this->examBeforeSaveValidator->execute($examDto);

        return DB::transaction(function () use ($examDto, $employee, $duration) {
            $exam = Exam::create($this->getAttributes($examDto, $employee, $duration));
            $exam->examiners()->attach($examDto->examiners);

            return $exam;
        });
    }

    protected function getAttributes(
        ExamDto $examDto,
        Employee $employee,
        int $duration
    ): array {
        return [
            'begin_time' => $examDto->beginTime,
            'address_id' => $examDto->addressId,
            'capacity' => $examDto->capacity,
            'exam_type_id' => $examDto->examTypeId,
            'comment' => $examDto->comment,
            'creator_id' => $employee->id,
            'end_time' => $examDto->beginTime->copy()->addMinutes($duration),
            'center_id' => $employee->center_id,
        ];
    }
}
