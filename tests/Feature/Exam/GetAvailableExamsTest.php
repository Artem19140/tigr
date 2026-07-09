<?php

namespace Tests\Feature\Exam;

use App\Modules\Exam\GetAvailableExams;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ForeignNational;
use App\Modules\Shared\ExamSettings;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetAvailableExamsTest extends TestCase
{
    use RefreshDatabase;
    protected Carbon $enrollmentTimeClosed;

    protected GetAvailableExams $query;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(
            Carbon::parse('2026-01-01 10:00:00')
        );
        $this->enrollmentTimeClosed = Carbon::now()->addMinutes(
            ExamSettings::enrollmentCloseBeforeExamMinutes()
        );

        $this->query = app(GetAvailableExams::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_success(): void
    {
        $exam = Exam::factory()
            ->create([
                'begin_time' => $this->enrollmentTimeClosed->addMinute()
            ]);
        $exams = $this->query->execute($exam->exam_type_id);
        $this->assertNotEmpty($exams);
    }

    public function test_fail_enrollment_window_closed(): void
    {
        $exam = Exam::factory()
            ->create([
                'begin_time' => $this->enrollmentTimeClosed->subMinute()
            ]);
        $exams = $this->query->execute($exam->exam_type_id);
        $this->assertEmpty($exams);
    }

    public function test_fail_full_enrollment(): void
    {
        $capacity = 4;

        $exam = Exam::factory()
            ->create([
                'begin_time' => $this->enrollmentTimeClosed->subMinute(),
                'capacity' => $capacity,
            ]);

        Enrollment::factory($capacity)->create([
            'exam_id' => $exam->id,
        ]);

        $exams = $this->query->execute($exam->exam_type_id);
        $this->assertEmpty($exams);
    }

    public function test_fail_cancelled_exam(): void
    {
        $exam = Exam::factory()
            ->create([
                'begin_time' => $this->enrollmentTimeClosed->addMinute(),
                'cancelled_at' => Carbon::now(),
            ]);

        $exams = $this->query->execute($exam->exam_type_id);

        $this->assertEmpty($exams);
    }

    public function test_fail_has_exsiting_enrollment(): void
    {
        $exam = Exam::factory()
            ->create([
                'begin_time' => $this->enrollmentTimeClosed->addMinute(),
            ]);

        $foreignNational = ForeignNational::factory()
            ->create();

        Enrollment::factory()->create([
            'exam_id' => $exam->id,
            'foreign_national_id' => $foreignNational->id,
        ]);

        $exams = $this->query->execute(
            $exam->exam_type_id,
            $foreignNational->id
        );

        $this->assertEmpty($exams);
    }
}
