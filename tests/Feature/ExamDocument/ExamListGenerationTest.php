<?php

namespace Tests\Feature\ExamDocument;

use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\Exam;
use Carbon\Carbon;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamListGenerationTest extends TestCase
{
    use RefreshDatabase;
    protected Employee $actor;
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->actor = Employee::factory()->examiner()->create();
        Carbon::setTestNow(now());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_success_list_generation(): void
    {
        $enrollment = Enrollment::factory()->create();
        $exam = Exam::factory()
            ->create();
        $exam->enrollments()->save($enrollment);
        $exam->examiners()->attach($this->actor);
        $response = $this
            ->actingAs($this->actor)
            ->getJson(route('exam.documents.list', ['exam' => $exam]));
        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
