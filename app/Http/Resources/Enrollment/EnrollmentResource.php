<?php

namespace App\Http\Resources\Enrollment;

use App\Http\Resources\Exam\ExamShortResource;
use App\Http\Resources\ForeignNational\ForeignNationalResource;
use App\Modules\Enrollment\EnrollmentActionsBuilder;
use App\Modules\Exam\ExamResultResolver;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
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
            'hasPayment' => $this->hasPayment(),
            'exam' => $this->when(
                $request->routeIs('foreign-nationals.show'), 
                new ExamShortResource($this->whenLoaded('exam'))
            ) ,
            'foreignNational' => new ForeignNationalResource($this->whenLoaded('foreignNational')),
            'regNumber' => $this->reg_number,
            'cancelledAt' => $this->isCancelled(),
            'examResult' => app(ExamResultResolver::class)->execute(
                $this->resource->attempt,
                $this->resource->exam
            ),
            'actions' => app(EnrollmentActionsBuilder::class)->build(
                $this->resource,
                $this->resource->exam,
                $request->user()
            )
        ];
    }
}
