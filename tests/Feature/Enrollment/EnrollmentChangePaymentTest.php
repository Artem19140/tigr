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
        $this->employee = Employee::factory()
            ->operator()
            ->create();
        Carbon::setTestNow(now());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    protected function putPayment(
        int $enrollmentId,
        bool $status
    )
    {
        return $this->actingAs($employee ?? $this->employee)
            ->put(route('enrollments.payment-change', [
                'enrollment' => $enrollmentId,
                'status' => $status
            ]));
    }

    public function test_success(): void
    {
        $paymentStatus = true;
        $expectedPaymentStatus = false;

        $this->withoutExceptionHandling();

        $exam = Exam::factory()->inFuture()->create();

        $enrollment = Enrollment::factory()->create([
            'exam_id' => $exam->id,
            'has_payment' => $paymentStatus
        ]);


        $response = $this->putPayment(
            $enrollment->id, 
            $expectedPaymentStatus
        );

        $enrollment->refresh();

        $this->assertEquals($expectedPaymentStatus, $enrollment->has_payment);

        $response->assertRedirectBack();
    }

    public function test_fail_has_attempt(): void
    {
        $paymentStatus = true;
        
        $enrollment = Enrollment::factory()
            ->has(Attempt::factory())
            ->create([
                'has_payment' => $paymentStatus
            ]);

        $response = $this->putPayment($enrollment->id, ! $paymentStatus);

        $response->assertRedirectBack();

        $this->assertEquals($paymentStatus, $enrollment->has_payment);
    }

    public function test_fail_past_exam(): void
    {
        $paymentStatus = true;
        $exam = Exam::factory()
            ->inPast()
            ->create();

        $enrollment = Enrollment::factory()->create([
            'exam_id' => $exam->id,
            'has_payment' => $paymentStatus
        ]);

        $response = $this->putPayment($enrollment->id, !  $paymentStatus);

        $response->assertRedirectBack();

        $this->assertEquals($paymentStatus, $enrollment->has_payment);
    }
}
