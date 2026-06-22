<?php

namespace Database\Seeders\Local;

use App\Models\Center;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $center = Center::firstWhere('inn', '1833010750');
        $password = Hash::make('12345678');
        for($i = 0; $i < 6; $i++){
            Employee::firstOrCreate(
                [
                    'email' =>"$i@udsu.ru"
                ],    
                [
                    'surname' => 'f',
                    'name' => 'n',
                    'patronymic'=> 'o',
                    'job_title' => '3s',
                    'center_id' => $center->id,
                    'email' => "$i@udsu.ru",
                    'has_to_change_password' => false,
                    'password' => $password,
                ]);
        }

        // Employee::factory()
        //     ->create([
        //         'surname' => 'тестовый',
        //         'name' => 'тестовый',
        //         'center_id' => $center,
        //         'email' => "test@udsu.ru",
        //         'has_to_change_password' => false,
        //         'password' => $password,
        //     ]);
        
    }
}
