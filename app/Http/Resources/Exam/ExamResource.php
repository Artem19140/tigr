<?php

namespace App\Http\Resources\Exam;

use App\Domain\Exam\Resolver\ExamStatusResolver;
use App\Domain\Exam\Rules\ExamCancellRules;
use App\Domain\Exam\Rules\ExamEditRules;
use App\Domain\ExamDocument\ExamDocumentRules;
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
            'endTime' => $this->begin_time_local->copy()->addMinutes($this->duration)->toIso8601String(),
            'enrollments' => EnrollmentExamShowResource::collection($this->whenLoaded('enrollments')),
            'sessionNumber' => $this->session,
            'capacity' => $this->capacity,
            'comment' => $this->comment,
            'group' => $this->group,
            'examiners' => EmployeeResource::collection($this->whenLoaded('examiners')),
            'name' => $this->whenLoaded('type', fn () => $this->type->name),
            'examTypeId' => $this->whenLoaded('type', fn () => $this->type->id),
            'address' => $this->whenLoaded('address', fn () => $this->address->address),
            'addressId' => $this->whenLoaded('address', fn () => $this->address->id),
            'creator' => new EmployeeResource($this->whenLoaded('creator')),
            'createdAt' => $this->created_at,
            'enrollmentsCount' => $this->whenCounted('enrollments_count'),
            'status' => app(ExamStatusResolver::class)->execute($exam),
            'availability' => $this->availability($exam),
            'permissions' => $this->permissions($employee, $exam)
        ];
    }

    protected function permissions(
        Employee $employee, 
        Exam $exam
    ): array
    {
        return [
            'documents' => [
                'codes' => $employee->can('examiner', $exam),
                'protocol' => $employee->can('protocol', $exam),
                'results' => $employee->can('results', $exam),
                'list' => $employee->can('list', $exam),
            ],
            'actions' => [
                'edit' => $employee->can('update', $exam),
                'cancell' => $employee->can('delete', $exam),
            ],
            'enrollments' => [
                'view' => $employee->can('viewAny', Enrollment::class),
                'statement' => $employee->can('statementAny', Enrollment::class),
                'payment' => $employee->can('paymentAny', Enrollment::class),
            ],
            'videos' => [
                'view' => $employee->can('video', $exam)
            ]

        ];
    }

    protected function availability(Exam $exam): array
    {
        return [
            'actions' => [
                'cancell' => app(ExamCancellRules::class)->check($exam)->available,
                'edit' => app(ExamEditRules::class)->check($exam)->available,
            ],  
            'documents' => app(ExamDocumentRules::class)->resolve($this->resource)
        ];
    }
}
