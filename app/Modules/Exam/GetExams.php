<?php

namespace App\Modules\Exam;

use App\Http\Dto\ExamIndexDto;
use App\Models\Employee;
use App\Models\Exam;
use App\Modules\Shared\CenterData;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;

class GetExams
{
    public function execute(
        ExamIndexDto $dto,
        Employee $employee
    ): Paginator
    {
        $examTypeId = $dto->examTypeId ?? false;
        $dateFrom =  $dto->dateFrom ?? false;
        $dateTo =  $dto->dateTo ?? false;
        $addressId =  $dto->addressId ?? false;
        $cancelled =  $dto->cancelled ?? false;
        $perPage = 10;
        
        $id = $dto->id ?? false;

        $query = Exam::with(['type'])
            ->withCount(['enrollments']);


        $query->visibleFor($employee);

        $query->when(
            $id, 
            fn ($q) => $q->where('id', $id)
        );

        $query->when(
            $examTypeId, 
            fn ($q) => $q->where('exam_type_id', $examTypeId)
        );

        $query->when(
            $dateFrom, 
            fn ($q) => $q->where(
                'begin_time', 
                '>=', 
                $this->dateFromStartOfDay($dateFrom)
            )
        );

        $query->when(
            $dateTo, 
            fn ($q) => $q->where(
                'begin_time', 
                '<', 
                $this->dateToEndOfDay($dateTo)
            )
        );

        $query->when(
            $addressId, 
            fn ($q) => $q->where('address_id', $addressId)
        );

        $cancelled 
            ? $query->whereNotNull('cancelled_at') 
            : $query->notCancelled();

        $query->latest('begin_time');

        return $query->simplePaginate($perPage)
            ->withQueryString();
    }

    protected function dateFromStartOfDay(
        Carbon $dateFrom
    ): Carbon {
        return $dateFrom->copy()
            ->setTimezone(
                CenterData::timeZone()
            )
            ->startOfDay()
            ->utc();
    }

    protected function dateToEndOfDay(
        Carbon $dateTo
    ): Carbon {
        return $dateTo->copy()
            ->setTimezone(
                CenterData::timeZone()
            )
            ->endOfDay()
            ->utc();
    }
}
