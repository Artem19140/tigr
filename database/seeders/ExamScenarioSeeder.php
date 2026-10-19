<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Attempt;
use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamScenarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $capacity = 20;
        $address = Address::first();
        $creator = Employee::first();
        $exam = Exam::factory()->create([
            'begin_time' => now()->addMinutes(5),
            'end_time' => now()->addMinutes(95),
            'capacity' => $capacity,
            'exam_type_id' => ExamType::firstWhere('level', 1),
            'address_id' => $address ? $address->id : Address::factory()
        ]);

        Enrollment::factory($capacity)
            ->for($exam)
            ->for($creator, 'creator')
            ->has(
                Attempt::factory()
                    ->for($exam, 'exam')
                    
            )
            ->create();

       
    }
}
