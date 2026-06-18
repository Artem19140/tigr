<?php

namespace App\Http\Resources\Enrollment;

use App\Http\Resources\ForeignNational\ForeignNationalResource;
use App\Models\Enrollment;
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
            'examResult' => $this->exam_result,
            'availability' => [
                'payment' => $this->payment_available ?? false
            ],
            'permissions' => [
                'payment' => $request->user()->can('paymentAny', Enrollment::class),
                'statement' => $request->user()->can('statementAny', Enrollment::class)
            ]
        ];
    }
}
