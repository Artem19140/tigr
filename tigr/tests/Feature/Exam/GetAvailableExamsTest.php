<?php

namespace Tests\Feature\Exam;

use App\Domain\Center\CenterContext;
use App\Domain\Exam\Query\GetAvailableExamsQuery;
use App\Models\Center;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ForeignNational;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class GetAvailableExamsTest extends TestCase
{
    use RefreshDatabase;

    protected Center $center;

    protected Carbon $enrollmentTimeClosed;

    protected GetAvailableExamsQuery $query;

    protected function setUp(): void
    {
        parent::setUp();
        $this->center = Center::factory()->create();
        Carbon::setTestNow(
            Carbon::parse('2026-01-01 10:00:00')
        );
        $this->enrollmentTimeClosed = Carbon::now()->addMinutes(Enrollment::CLOSE_BEFORE_START_MINUTES);

        $mock = Mockery::mock(CenterContext::class);
        $mock->shouldReceive('id')->andReturn($this->center->id);

        $this->app->instance(CenterContext::class, $mock);
        $this->query = app(GetAvailableExamsQuery::class);
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
                'begin_time' => $this->enrollmentTimeClosed->addMinute(),
                'center_id' => $this->center->id,
            ]);
        $exams = $this->query->execute($exam->exam_type_id);
        $this->assertNotEmpty($exams);
    }

    public function test_fail_enrollment_window_closed(): void
    {
        $exam = Exam::factory()
            ->create([
                'begin_time' => $this->enrollmentTimeClosed->subMinute(),
                'center_id' => $this->center->id,
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
                'center_id' => $this->center->id,
                'capacity' => $capacity,
            ]);

        Enrollment::factory($capacity)->create([
            'center_id' => $this->center->id,
            'exam_id' => $exam->id,
        ]);

        $exams = $this->query->execute($exam->exam_type_id);
        $this->assertEmpty($exams);
    }

    public function test_fail_different_centers(): void
    {
        $exam = Exam::factory()
            ->create([
                'begin_time' => $this->enrollmentTimeClosed->addMinute(),
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
            ->create([
                'center_id' => $this->center->id,
            ]);

        Enrollment::factory()->create([
            'center_id' => $this->center->id,
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
