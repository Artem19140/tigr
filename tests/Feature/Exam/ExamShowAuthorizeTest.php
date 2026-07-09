<?php

namespace Tests\Feature\Exam;

use App\Enums\EmployeeRole;
use App\Models\Employee;
use App\Models\Exam;
use Carbon\Carbon;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamShowAuthorizeTest extends TestCase
{
    use RefreshDatabase;

    protected Exam $exam;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->exam = Exam::factory()
            ->create();
        Carbon::setTestNow();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_success_examiner(): void
    {
        $employee = Employee::factory()->examiner()->create();
        $this->exam->examiners()->attach($employee);
        $response = $this->actingAs($employee)
            ->getJson(route('exams.show', ['exam' => $this->exam]));

        $response->assertStatus(200);
    }

    public function test_fail_no_attach_examiner(): void
    {
        $employee = Employee::factory()
            ->examiner()
            ->create();

        $response = $this->actingAs($employee)
            ->getJson(route('exams.show', ['exam' => $this->exam]));
        $response->assertStatus(403);
    }

    public function test_success_exam_show_authorization(): void
    {
        $allowedRoles = [
            EmployeeRole::Operator,
            EmployeeRole::Director,
            EmployeeRole::PlatformAdmin,
            EmployeeRole::Scheduler,
            EmployeeRole::VideoRecordOperator,
        ];
        foreach ($allowedRoles as $role) {
            $employee = Employee::factory()
                ->withRole($role)
                ->create();
            $this->assertTrue(
                $employee->can('view', $this->exam),
                "Role {$role->value} should be allowed"
            );
        }

    }

    public function test_fail_exam_show_authorization(): void
    {
        $allowedRoles = [
            EmployeeRole::CenterAdmin,
            
        ];
        foreach ($allowedRoles as $role) {
            $employee = Employee::factory()
                ->withRole($role)
                ->create();
            $this->assertFalse(
                $employee->can('view', $this->exam),
                "Role {$role->value} should not be allowed"
            );
        }

    }
}
