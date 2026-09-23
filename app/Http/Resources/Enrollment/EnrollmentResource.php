<?php

namespace App\Http\Resources\Enrollment;

use App\Http\Resources\Exam\ExamShortResource;
use App\Http\Resources\ForeignNational\ForeignNationalResource;
use App\Models\Enrollment;
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
            'exam' => $this->when($request->routeIs('foreign-nationals.show'), new ExamShortResource($this->whenLoaded('exam'))) ,
            'foreignNational' => new ForeignNationalResource($this->whenLoaded('foreignNational')),
            'examResult' => app(ExamResultResolver::class)->execute($this->resource),
            'actions' => [
                'payment' => [
                    'disabled' => ! app(EnrollmentPaymentRules::class)->check($this->resource)->available,
                    'url' => $request->user()->can('paymentAny', Enrollment::class)
                        ? route('enrollments.payment-change', [
                            'enrollment' => $this->resource
                        ], false)
                        : null
                ],
                'statement' => [
                    'url' => $request->user()->can('statementAny', Enrollment::class)
                        ? route('enrollments.statements', [
                            'enrollment' => $this->resource
                        ], false)
                        : null
                ]
            ]
        ];
    }
}
