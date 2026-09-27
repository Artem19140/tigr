<?php

namespace Tests\Feature\ExamDocument;

use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Modules\Shared\ExamSettings;
use Carbon\Carbon;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamCodesGenerationTest extends TestCase
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

    public function test_success_codes_generation(): void
    {
        $this->withoutExceptionHandling();

        $exam = Exam::factory()
            ->create([
                'begin_time' => Carbon::now()->addHour(),
            ]);

        $enrollment = Enrollment::factory()->create();
        $exam->enrollments()->save($enrollment);
        $exam->examiners()->attach($this->actor);

        $response = $this
            ->actingAs($this->actor)
            ->getJson(route('exams.documents.codes', ['exam' => $exam]));
            
        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_closed_generation_window_before_exam_day(): void
    {

        $exam = Exam::factory()
            ->create([
                'begin_time' => Carbon::now()->addDay(),
            ]);

        $enrollment = Enrollment::factory()->create();
        
        $exam->enrollments()->save($enrollment);
        $exam->examiners()->attach($this->actor);

        $response = $this
            ->actingAs($this->actor)
            ->getJson(route('exams.documents.codes', ['exam' => $exam]));
            
        $response->assertBadRequest();
    }

    public function test_closed_generation_window_after_codes_ttl(): void
    {

        $exam = Exam::factory()
            ->create([
                'begin_time' => Carbon::now()->subMinutes(ExamSettings::codesTtlMinutes() + 1),
            ]);

        $enrollment = Enrollment::factory()->create();
        $exam->enrollments()->save($enrollment);
        $exam->examiners()->attach($this->actor);

        $response = $this
            ->actingAs($this->actor)
            ->getJson(route('exams.documents.codes', ['exam' => $exam]));
            
        $response->assertBadRequest();
    }
}
