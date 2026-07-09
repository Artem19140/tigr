<?php

namespace Tests\Feature\Enrollment;

use App\Models\Attempt;
use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\Exam;
use Carbon\Carbon;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentChangePaymentTest extends TestCase
{
    use RefreshDatabase;
    protected Employee $employee;
    protected string $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->employee = Employee::factory()->operator()->create();
        Carbon::setTestNow(now());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    protected function putPayment(int $enrollmentId, ?Employee $user = null)
    {
        return $this->actingAs($employee ?? $this->employee)
            ->putJson("/enrollments/$enrollmentId/payment");
    }

    public function test_success(): void
    {
        $this->withoutExceptionHandling();
        $exam = Exam::factory()->inFuture()->create();
        $enrollment = Enrollment::factory()->create([
            'exam_id' => $exam->id,
        ]);

        $response = $this->putPayment($enrollment->id);

        $response->assertStatus(200);
    }

    public function test_success_attached_examiner(): void
    {
        $exam = Exam::factory()->inFuture()->create();

        $enrollment = Enrollment::factory()->create([
            'exam_id' => $exam->id
        ]);
        $employee = Employee::factory()
            ->examiner()
            ->create();
        $exam->examiners()->attach($employee);
        $response = $this->putPayment($enrollment->id, $employee);

        $response->assertOk();
    }

    public function test_fail_has_attempt(): void
    {
        $exam = Exam::factory()->inFuture()->create();

        $enrollment = Enrollment::factory()
            ->has(Attempt::factory())
            ->create([
                'exam_id' => $exam->id
            ]);
        $response = $this->putPayment($enrollment->id);

        $response->assertBadRequest();
    }

    public function test_fail_past_exam(): void
    {
        $exam = Exam::factory()->inPast()->create();
        $enrollment = Enrollment::factory()->create([
            'exam_id' => $exam->id
        ]);
        $response = $this->putPayment($enrollment->id);

        $response->assertBadRequest();
    }
}
