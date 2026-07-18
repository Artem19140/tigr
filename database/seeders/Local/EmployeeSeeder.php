<?php

namespace Database\Seeders\Local;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('12345678');
        for($i = 0; $i < 6; $i++){
            Employee::firstOrCreate(
                [
                    'email' =>"$i@udsu.ru"
                ],    
                [
                    'surname' => 'f',
                    'name' => 'Татьяна',
                    'patronymic'=> 'o',
                    'email' => "$i@udsu.ru",
                    'password' => $password,
                ]
            );
        }        
    }
}
