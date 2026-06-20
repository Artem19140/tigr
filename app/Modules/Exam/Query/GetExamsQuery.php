<?php

namespace App\Modules\Exam\Query;

use App\Http\Dto\ExamIndexDto;
use App\Modules\Center\CenterContext;
use App\Models\Employee;
use App\Models\Exam;
use Illuminate\Contracts\Pagination\Paginator;

class GetExamsQuery
{
    public function __construct(
        protected CenterContext $centerContext
    ){}
    public function execute(
        ExamIndexDto $dto,
        Employee $employee
    ): Paginator
    {
        $examTypeId = $dto->examTypeId ?? false;
        $dateFrom =  $dto->dateFrom?? false;
        $dateTo =  $dto->dateTo ?? false;
        $addressId =  $dto->addressId ?? false;
        $cancelled =  $dto->cancelled ?? false;
        $perPage = 10;

        $id = $dto->id ?? false;

        $query = Exam::with(['type', 'center'])
            ->withCount(['enrollments']);

        $query->forCenter($this->centerContext->id());

        $query->visibleFor($employee);

        $query->when($id, fn ($q) => $q->where('id', $id)
        );

        $query->when($examTypeId, fn ($q) => $q->where('exam_type_id', $examTypeId)
        );

        $query->when($dateFrom, fn ($q) => $q->where('begin_time', '>=', $dateFrom->startOfDay())
        );

        $query->when($dateTo, fn ($q) => $q->where('begin_time', '<', $dateTo->endOfDay())
        );

        $query->when($addressId, fn ($q) => $q->where('address_id', $addressId)
        );

        $cancelled ? $query->cancelled() : $query->notCancelled();

        $query->latest('begin_time');

        return $query->simplePaginate($perPage)
            ->withQueryString();
    }
}
