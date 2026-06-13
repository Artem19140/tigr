<?php

namespace App\Domain\Exam\Query;

use App\Domain\Center\CenterContext;
use App\Models\Employee;
use App\Models\Exam;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;

class GetExamsQuery
{
    public function __construct(
        protected CenterContext $centerContext
    ){}
    public function execute(
        array $data,
        Employee $employee
    ): Paginator
    {
        $examTypeId = $data['examTypeId'] ?? false;
        $dateFrom = $data['dateFrom'] ?? false;
        $dateTo = $data['dateTo'] ?? false;
        $addressId = $data['addressId'] ?? false;
        $cancelled = $data['cancelled'] ?? false;
        $perPage = $data['perPage'] ?? 10;

        $id = $data['id'] ?? false;

        $query = Exam::with(['type', 'center'])
            ->withCount(['enrollments']);

        $query->forCenter($this->centerContext->id());

        $query->visibleFor($employee);

        $query->when($id, fn ($q) => $q->where('id', $id)
        );

        $query->when($examTypeId, fn ($q) => $q->where('exam_type_id', $examTypeId)
        );

        $query->when($dateFrom, fn ($q) => $q->where('begin_time', '>=', Carbon::parse($dateFrom)->startOfDay())
        );

        $query->when($dateTo, fn ($q) => $q->where('begin_time', '<', Carbon::parse($dateTo)->endOfDay())
        );

        $query->when($addressId, fn ($q) => $q->where('address_id', $addressId)
        );

        $cancelled ? $query->cancelled() : $query->notCancelled();

        $query->latest('begin_time');

        return $query->simplePaginate($perPage)
            ->withQueryString();
    }
}
