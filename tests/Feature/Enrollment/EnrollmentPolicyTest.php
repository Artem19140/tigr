<?php

namespace Tests\Feature\Enrollment;

use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\Exam;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentPolicyTest extends TestCase
{
    use RefreshDatabase;
    protected Enrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enrollment = Enrollment::factory()->create();
        $this->seed(RolesSeeder::class);
    }

    public function test_enrollment_permissions_operator_base_access(): void
    {
        $operator = Employee::factory()
            ->operator()
            ->create();
        $this->assertTrue($operator->can('viewAny', Enrollment::class));
        $this->assertTrue($operator->can('create', Enrollment::class));
        $this->assertTrue($operator->can('payment', $this->enrollment));
        $this->assertTrue($operator->can('statement', $this->enrollment));
    }

    public function test_permission_examiner_change_payment(): void
    {
        $examinerA = Employee::factory()
            ->examiner()
            ->create();
        $examinerB = Employee::factory()
            ->examiner()
            ->create();
        $exam = Exam::factory()->create();
        $exam->enrollments()->save($this->enrollment);
        $exam->examiners()->attach($examinerA);
        $this->assertTrue($examinerA->can('payment', $this->enrollment));
        $this->assertFalse($examinerB->can('payment', $this->enrollment));
    }
}
