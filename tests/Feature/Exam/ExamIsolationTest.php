<?php

namespace Tests\Feature\Exam;

use App\Models\Employee;
use App\Models\Exam;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_success_exam_isolaiton(): void
    {
        $this->seed(RolesSeeder::class);

        $exam = Exam::factory()->create();

        $operator = Employee::factory()
            ->operator()->create();
        $examinerA = Employee::factory()
            ->examiner()
            ->create();
        $examinerB = Employee::factory()
            ->examiner()
            ->create();

        $exam->examiners()->attach($examinerA);
        
        $this->assertCount(1, Exam::query()->visibleFor($examinerA)->get());
        $this->assertCount(0, Exam::query()->visibleFor($examinerB)->get());
        $this->assertCount(1, Exam::query()->visibleFor($operator)->get());
    }
}
