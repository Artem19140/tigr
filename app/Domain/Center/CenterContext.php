<?php

namespace App\Domain\Center;

class CenterContext
{
    public function id(): ?int
    {
        $employee = auth()->user();
        if (! $employee || $employee->isPlatformAdmin()) {
            if(session()->has('center_id')){
                return session('center_id', null);
            }
            return null;
        }

        return $employee->center_id;
    }
}
