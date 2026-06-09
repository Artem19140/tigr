<?php

namespace App\Http\Resources\Enrollment;

use App\Domain\Enrollment\Rules\EnrollmentPaymentRules;
use App\Domain\Exam\Resolver\ExamResultResolver;
use App\Http\Resources\ForeignNational\ForeignNationalResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentExamShowResource extends JsonResource
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
            'foreignNational' => new ForeignNationalResource($this->whenLoaded('foreignNational')),
            'examResult' => $this->when(
                $this->relationLoaded('exam') &&
                $this->relationLoaded('attempt'),
                fn () => app(ExamResultResolver::class)->execute($this->resource),
            ),
            'availability' => [
                'payment' => app(EnrollmentPaymentRules::class)->check($this->resource)->available
            ]
        ];
    }
}
