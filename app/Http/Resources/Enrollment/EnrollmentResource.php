<?php

namespace App\Http\Resources\Enrollment;

use App\Http\Resources\Exam\ExamShortResource;
use App\Http\Resources\ForeignNational\ForeignNationalResource;
use App\Modules\Enrollment\EnrollmentPaymentRules;
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
            'hasPayment' => $this->has_payment,
            'exam' => new ExamShortResource($this->whenLoaded('exam')),
            'foreignNational' => new ForeignNationalResource($this->whenLoaded('foreignNational')),
            'examResult' => app(ExamResultResolver::class)->execute($this->resource),
            'availability' => [
                'payment' => app(EnrollmentPaymentRules::class)->check($this->resource)->available,
            ],
            'permissions' => [
                'payment' => $request->user()->can('payment', $this->resource),
                'statement' => $request->user()->can('statement', $this->resource),
            ]
        ];
    }
}
