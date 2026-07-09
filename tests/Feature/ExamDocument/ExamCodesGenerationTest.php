<?php

namespace Tests\Feature\ExamDocument;

use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\Exam;
use Carbon\Carbon;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamCodesGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected Employee $actor;
    protected Enrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);

        $this->enrollment = Enrollment::factory()->create();
        $this->actor = Employee::factory()->examiner()->create();

        Carbon::setTestNow(
            Carbon::parse('2026-01-01 10:00:00')
        );

    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_success_codes_generation(): void
    {
        $this->withoutExceptionHandling();

        $exam = Exam::factory()
            ->create([
                'begin_time' => Carbon::now()->addHour(),
            ]);

        $exam->enrollments()->save($this->enrollment);
        $exam->examiners()->attach($this->actor);

        $response = $this
            ->actingAs($this->actor)
            ->getJson(route('exam.documents.codes', ['exam' => $exam]));
        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
