<?php

namespace App\Domain\Center;

class CenterContext
{
    public function id(): ?int
    {
        $employee = auth()->user();
        if (! $employee || $employee->isPlatformAdmin()) {
            return null;
        }

        return $employee->center_id;
    }
}
