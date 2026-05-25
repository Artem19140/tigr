<?php

namespace App\Domain\Exam\Query;

use App\Domain\Center\CenterContext;
use App\Enums\EmployeeRole;
use App\Models\Address;
use App\Models\Employee;
use App\Models\ExamType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class ExamCreateDataQuery
{
    public function __construct(
        protected CenterContext $centerContext
    ){}
    public function execute():array
    {
        $examiners = Employee::whereHas('roles', function (Builder $query) {
                $query->where('name', EmployeeRole::Examiner);
            })
            ->active()
            ->forCenter($this->centerContext->id())
            ->get();

        $adresses = Address::where('is_active', true)
            ->forCenter($this->centerContext->id())
            ->get();

        $examTypes = Cache::rememberForever('exam_types', function () {
            return ExamType::all();
        });

        return [
            'addresses' => $adresses,
            'examTypes' => $examTypes,
            'examiners' => $examiners,
        ];
    }
}