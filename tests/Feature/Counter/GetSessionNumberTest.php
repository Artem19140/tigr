<?php

namespace Tests\Feature\Counter;

use App\Modules\Counter\GetSessionNumberQuery;
use App\Models\Attempt;
use App\Models\Center;
use App\Models\Enrollment;
use App\Models\Exam;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetSessionNumberTest extends TestCase
{
    use RefreshDatabase;

    protected $action;

    protected int $value;
    protected Center $center;

    protected function setUp(): void
    {
        parent::setUp();
        $this->center = Center::factory()->create();
        $this->action = app(GetSessionNumberQuery::class);
        Carbon::setTestNow(Carbon::create(2025, 5, 1, 0, 0, 0));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow(); 
    }

    public function test_success(): void
    {
        $count = 15;
        $this->createExams($count);

        $exam = Exam::factory()->create([
            'begin_time' => Carbon::now()->addDays($count + 1),
            'center_id' => $this->center->id
        ]);

        $enrollment = Enrollment::factory()
            ->create([
                'exam_id' => $exam->id,
                'center_id' => $this->center->id
            ]);

        Attempt::factory()->create([
            'enrollment_id' => $enrollment->id,
            'exam_id' => $exam->id,
            'center_id' => $this->center->id
        ]);

        $sessionNumber = $this->action->execute($exam->begin_time);
        $this->assertEquals($sessionNumber, $count + 1);
    }

    public function test_change_year(): void
    {
        $countFirst = 15;
        $this->createExams($countFirst);
        $examFirst = Exam::factory()->create([
            'begin_time' => Carbon::now()->addDays($countFirst + 1),
            'center_id' => $this->center->id
        ]);

        $sessionNumber = $this->action->execute($examFirst->begin_time);
        $this->assertEquals($sessionNumber, $countFirst + 1);

        Carbon::setTestNow(Carbon::create(2026, 5, 1, 0, 0, 0));
        $countSecond = 17;

        $this->createExams(17);

        $examSecond = Exam::factory()->create([
            'begin_time' => Carbon::now()->addDays($countSecond + 1),
            'center_id' => $this->center->id
        ]);

        $sessionNumber = $this->action->execute($examSecond->begin_time);

        $this->assertEquals($sessionNumber, $countSecond + 1);
        Carbon::setTestNow();
    }

    public function test_with_cancelled_exam(): void
    {
        $count = 15;
        $this->createExams(15);

        $exam = Exam::factory()->cancelled()->create([
            'begin_time' => Carbon::now()->addDays($count + 1),
            'center_id' => $this->center->id
        ]);

        $exam = Exam::factory()->create([
            'begin_time' => Carbon::now()->addDays($count + 2),
            'center_id' => $this->center->id
        ]);

        $enrollment = Enrollment::factory()
            ->create([
                'exam_id' => $exam->id,
                'center_id' => $this->center->id
            ]);

        Attempt::factory()->create([
            'enrollment_id' => $enrollment->id, 
            'exam_id' => $exam->id,
            'center_id' => $this->center->id
        ]);

        $sessionNumber = $this->action->execute($exam->begin_time);
        $this->assertEquals($sessionNumber, $count + 1);
    }

    public function test_with_no_attempts(): void
    {
        $count = 15;
        $this->createExams(15);

        Exam::factory()->create([
            'begin_time' => Carbon::now()->addDays($count + 1),
            'center_id' => $this->center->id
        ]);

        $exam = Exam::factory()->create([
            'begin_time' => Carbon::now()->addDays($count + 2),
            'center_id' => $this->center->id
        ]);

        $enrollment = Enrollment::factory()
            ->create([
                'exam_id' => $exam->id,
                'center_id' => $this->center->id
            ]);

        Attempt::factory()->create([
            'enrollment_id' => $enrollment->id, 
            'exam_id' => $exam->id,
            'center_id' => $this->center->id
        ]);

        $sessionNumber = $this->action->execute($exam->begin_time);
        $this->assertEquals($sessionNumber, $count + 1);
    }

    protected function createExams(int $count)
    {
        for ($i = 1; $i <= $count; $i++) {
            $exam = Exam::factory()->create([
                'begin_time' => Carbon::now()->addDays($i),
                'center_id' => $this->center->id
            ]);

            $enrollment = Enrollment::factory()
                ->create([
                    'exam_id' => $exam->id,
                    'center_id' => $this->center->id
                ]);

            Attempt::factory()->create([
                'enrollment_id' => $enrollment->id, 
                'exam_id' => $exam->id,
                'center_id' => $this->center->id
            ]);
        }
    }
}
