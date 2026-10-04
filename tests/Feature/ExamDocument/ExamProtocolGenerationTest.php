<?php

namespace Tests\Feature\ExamDocument;

use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\Exam;
use Carbon\Carbon;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamProtocolGenerationTest extends TestCase
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

        Carbon::setTestNow(
            Carbon::parse('2026-01-01 10:00:00')
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_success_exam_protocol_generation(): void
    {
        $this->withoutExceptionHandling();
        $enrollment = Enrollment::factory()->create();
        $exam = Exam::factory()
            ->create(
                [
                    'begin_time' => Carbon::now()->subMinutes(200)
                ]
            );
        $exam->enrollments()->save($enrollment);
        $exam->examiners()->attach($this->actor);

        $response = $this
            ->actingAs($this->actor)
            ->getJson(route('exams.documents.protocol', ['exam' => $exam]));
        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
