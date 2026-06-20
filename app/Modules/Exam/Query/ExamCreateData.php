<?php

namespace App\Modules\Exam\Query;

use App\Modules\Center\CenterContext;
use App\Enums\EmployeeRole;
use App\Models\Address;
use App\Models\Employee;
use App\Models\ExamType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class ExamCreateData
{
    public function __construct(
        protected CenterContext $centerContext
    ){}
    public function execute():array
    {
        $examiners = Employee::query()
            ->whereHas('roles', function (Builder $query) {
                $query->where('name', EmployeeRole::Examiner);
            })
            ->active()
            ->forCenter($this->centerContext->id())
            ->get();

        $addresses = Address::query()
            ->where('is_active', true)
            ->forCenter($this->centerContext->id())
            ->get();

        $examTypes = ExamType::cached();

        return [
            'addresses' => $addresses,
            'examTypes' => $examTypes,
            'examiners' => $examiners,
        ];
    }

    protected function cacheExaminers()
    {
        return Employee::whereHas('roles', function (Builder $query) {
                $query->where('name', EmployeeRole::Examiner);
            })
            ->active()
            ->forCenter($this->centerContext->id())
            ->get();
    }

    protected function cacheAddresses()
    {
        return Cache::rememberForever("addresses-center-{$this->centerContext->id()}", function(){
            return Address::where('is_active', true)
                ->forCenter($this->centerContext->id())
                ->get();
        });
    }
}