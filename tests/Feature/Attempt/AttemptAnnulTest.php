<?php

namespace Tests\Feature\Attempt;

use App\Models\Attempt;
use App\Models\Center;
use App\Models\Employee;
use App\Models\Exam;
use Carbon\Carbon;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttemptAnnulTest extends TestCase
{
    use RefreshDatabase;

    protected Employee $employee;

    protected Center $center;

    protected function setUp(): void
    {
        parent::setUp();

        $this->center = Center::factory()->create();

        $this->seed(RolesSeeder::class);

        $this->employee = Employee::factory()
            ->examiner()
            ->create([
                'center_id' => $this->center->id,
            ]);

        Carbon::setTestNow('2026-01-01 10:00:00');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_success_attempt_annulled(): void
    {
        $this->withoutExceptionHandling();

        $exam = Exam::factory()
            ->create([
                'center_id' => $this->center->id,
            ]);

        $exam->examiners()->attach($this->employee);

        $attempt = Attempt::factory()
            ->create([
                'exam_id' => $exam->id,
                'center_id' => $this->center->id,
            ]);

        $response = $this->actingAs($this->employee)
            ->putJson(route('attempts.annul', ['attempt' => $attempt]), [
                'annulReason' => 'Есть',
            ]);
        $attempt->refresh();

        $this->assertNotNull($attempt->finished_at);
        $this->assertNotNull($attempt->annulled_at);

        $response->assertNoContent();
    }

    public function test_fail_annul_repeated(): void
    {
        $exam = Exam::factory()
            ->inPast()
            ->create([
                'center_id' => $this->center->id,
            ]);

        $exam->examiners()
            ->attach($this->employee);

        $attempt = Attempt::factory()
            ->annulled()
            ->create([
                'exam_id' => $exam->id,
                'center_id' => $this->center->id,
                'annulled_at' => Carbon::now(),
            ]);

        $response = $this->actingAs($this->employee)
            ->putJson(route('attempts.annul', [
                'attempt' => $attempt,
            ]),
                [
                    'annulledReason' => 'Есть',
                ]);

        $response->assertBadRequest();
    }
}
