<?php

namespace Database\Seeders\Local;

use App\Enums\EmployeeRole;
use App\Models\Center;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $center = Center::firstWhere('inn', '1833010750');
        $password = Hash::make('123456789');
        $roles = EmployeeRole::cases();

        foreach ($roles as $role) {
            if ($role === EmployeeRole::PlatformAdmin) {
                continue;
            }
            Employee::factory()
                ->withRole($role)
                ->create([
                    'center_id' => $center,
                    'email' => $role->value.'@udsu.ru',
                    'has_to_change_password' => false,
                    'password' => $password,
                ]);
        }
    }
}
