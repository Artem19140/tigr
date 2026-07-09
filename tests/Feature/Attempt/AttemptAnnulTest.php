<?php

namespace Tests\Feature\Attempt;

use App\Models\Attempt;
use App\Models\Employee;
use App\Models\Exam;
use Carbon\Carbon;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttemptAnnulTest extends TestCase
{
    use RefreshDatabase;
    protected Employee $actor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesSeeder::class);

        $this->actor = Employee::factory()
            ->examiner()
            ->create();

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
            ->create();

        $exam->examiners()->attach($this->actor);

        $attempt = Attempt::factory()
            ->create([
                'exam_id' => $exam->id
            ]);

        $response = $this->actingAs($this->actor)
            ->putJson(route('attempts.destroy', ['attempt' => $attempt]), [
                'annulledReason' => 'Есть',
            ]);
            
        $attempt->refresh();

        $this->assertNotNull($attempt->finished_at);
        $this->assertNotNull($attempt->annulled_at);

        $response->assertNoContent();
    }

    public function test_fail_annul_repeated(): void
    {
        $exam = Exam::factory()
            ->create();

        $exam->examiners()
            ->attach($this->actor);

        $attempt = Attempt::factory()
            ->annulled()
            ->create([
                'exam_id' => $exam->id,
                'annulled_at' => Carbon::now(),
            ]);

        $response = $this->actingAs($this->actor)
            ->putJson(route('attempts.destroy', [
                'attempt' => $attempt,
            ]),
                [
                    'annulledReason' => 'Есть',
                ]);

        $response->assertBadRequest();
    }
}
