<?php

namespace App\Modules\Exam\Action;

use App\Modules\Exam\Rules\ExamEditRules;
use App\Modules\Exam\Validator\ExamBeforeSaveValidator;
use App\Exceptions\BusinessException;
use App\Http\Dto\ExamDto;
use App\Models\Exam;
use App\Support\ModelChangesLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class UpdateExam
{
    public function __construct(
        protected ExamEditRules $examEditRules,
        protected ExamBeforeSaveValidator $examBeforeSaveValidator,
        protected ModelChangesLogger $logger
    ) {}

    public function execute(
        Exam $exam,
        ExamDto $examDto,
    ): void {
        $result = $this->examEditRules->check($exam);
        
        if($result->isNotAvailable()){
            throw new BusinessException($result->message());
        }

        $this->examBeforeSaveValidator->execute($examDto, $exam->id);

        $exam = DB::transaction(function () use ($examDto, $exam) {

            $exam->update(
                $this->getAttributes($exam, $examDto)
            );
            $exam->examiners()->sync($examDto->examiners);
            $exam->save();

            $exam->load(['examiners', 'type', 'address']);
            $exam->loadCount('enrollments');

            return $exam;
        });

        $this->logger->log($exam);

    }

    protected function getAttributes(Exam $exam, ExamDto $examDto): array
    {
        $attributes = [];

        if (! $exam->enrollments()->exists()) {
            $attributes = [
                'begin_time' => $examDto->beginTime,
                'address_id' => $examDto->addressId,
                'capacity' => $examDto->capacity,
                'exam_type_id' => $examDto->examTypeId,
                'comment' => $examDto->comment,
                'end_time' => $examDto->beginTime->copy()->addMinutes($exam->duration),
            ];
        } else {
            if ($exam->enrollments()->count() > $examDto->capacity) {
                throw ValidationException::withMessages([
                    'capacity' => 'Запись на экзамен превышает вместимость',
                ]);
            }
            $attributes['capacity'] = $examDto->capacity;
        }
        $attributes['comment'] = $examDto->comment;

        return $attributes;
    }
}
