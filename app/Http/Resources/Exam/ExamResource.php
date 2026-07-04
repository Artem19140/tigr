<?php

namespace App\Http\Resources\Exam;

use App\Modules\ExamDocument\ExamDocumentRules;
use App\Http\Resources\Employee\EmployeeResource;
use App\Http\Resources\Enrollment\EnrollmentExamShowResource;
use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $employee = $request->user();
        $exam = $this->resource;

        return [
            'id' => $this->id,
            'cancelledReason' => $this->when($this->isCancelled(), $this->cancelled_reason),
            'cancelledAt' => $this->resource->cancelled_at,
            'beginTime' => $this->begin_time_local->copy()->toIso8601String(),
            'endTime' => $this->end_time_local->copy()->toIso8601String(),
            'enrollments' => EnrollmentExamShowResource::collection($this->whenLoaded('enrollments')),
            'sessionNumber' => $this->session,
            'capacity' => $this->capacity,
            'comment' => $this->comment,
            'group' => $this->group,
            'examiners' => EmployeeResource::collection($this->whenLoaded('examiners')),
            'name' => $this->whenLoaded('type', fn () => $this->type->name),
            'shortName' => $this->whenLoaded('type', fn () => $this->type->short_name),
            'address' => $this->whenLoaded('address', fn () => $this->address->address),
            'actions' => $this->actions($exam, $employee)
        ];
    }

    protected function actions(Exam $exam, Employee $employee)
    {
        return [
            ...app(ExamDocumentRules::class)->resolve($this->resource,  $employee),
            'enrollments' => [
                'view'=> [
                    'can' => $employee->can('viewAny', Enrollment::class)
                ],
                'statement'=> [
                    'can' => $employee->can('statementAny', Enrollment::class)
                ],
                'payment'=> [
                    'can' => $employee->can('paymentAny', Enrollment::class)
                ]
            ]
        ];
    }
}
