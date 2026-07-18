<?php

namespace App\Enums;

enum EmployeeRole: string
{
    case Operator = 'operator';
    case Scheduler = 'scheduler';
    case Examiner = 'examiner';
    case Director = 'director';
    case CenterAdmin = 'center_admin';
    case PlatformAdmin = 'platform_admin';

    public static function implode(self ...$roles)
    {
        $rolesValues = array_map(fn (EmployeeRole $role) => $role->value, $roles);

        return implode(',', $rolesValues);
    }

    public static function except(self ...$roles): array
    {
        return array_filter(
            self::cases(),
            fn (self $role) => ! \in_array($role, $roles, true)
        );
    }
}
