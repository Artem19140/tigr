<?php

namespace Tests\Feature\Exam;

use App\Models\Employee;
use App\Models\Exam;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamPolicyTest extends TestCase
{
    use RefreshDatabase;
    protected Exam $exam;
    protected function setUp(): void
    {
        parent::setUp();
        $this->exam = Exam::factory()->create();
        $this->seed(RolesSeeder::class);
    }

    public function test_operator_base_access(): void
    {
        $operator = Employee::factory()
            ->operator()
            ->create();

        $this->assertTrue($operator->can('viewAny', Exam::class));
        $this->assertTrue($operator->can('view', $this->exam));
        $this->assertTrue($operator->can('reports.frdo'));
        $this->assertTrue($operator->can('list', $this->exam));
    }

    public function test_scheduler_base_access(): void
    {
        $scheduler = Employee::factory()
            ->scheduler()
            ->create();
        $this->assertTrue($scheduler->can('delete', $this->exam));
        $this->assertTrue($scheduler->can('create', Exam::class));
        $this->assertTrue($scheduler->can('update', $this->exam));
        $this->assertTrue($scheduler->can('view', $this->exam));
    }

    public function test_examiner(): void
    {
        $examinerA = Employee::factory()
            ->examiner()
            ->create();
        $examinerB = Employee::factory()
            ->examiner()
            ->create();
        $this->exam->examiners()->attach($examinerA);
        $this->assertTrue($examinerA->can('examiner', $this->exam),
            'examiner should has access to exam'
        );

        $this->assertFalse($examinerB->can('examiner', $this->exam),
            'examiner should not has access to exam'
        );
    }
}
