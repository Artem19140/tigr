<?php

namespace Tests\Feature\Enrollment;

use App\Models\Employee;
use App\Models\Enrollment;
use Carbon\Carbon;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentStatementGenerationTest extends TestCase
{
    use RefreshDatabase;
    protected Employee $actor;
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->actor = Employee::factory()
            ->operator()
            ->create();
        Carbon::setTestNow(now());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_success_enrollment_statement_generating(): void
    {

        $enrollment = Enrollment::factory()->create();

        $response = $this->actingAs($this->actor)
            ->getJson(route('enrollments.statements', ['enrollment' => $enrollment]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertStatus(200);
    }

    public function test_fail_enrollment_statement_generating_another_center(): void
    {

        $enrollment = Enrollment::factory()->create();

        $response = $this->actingAs($this->actor)
            ->getJson(route('enrollments.statements', ['enrollment' => $enrollment]));

        $response->assertForbidden();
    }
}
