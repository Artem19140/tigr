<?php

namespace Tests\Feature\Exam;

use App\Modules\Exam\Action\CancelExam;
use App\Modules\Exam\Rules\ExamCancellRules;
use App\Exceptions\BusinessException;
use App\Models\Center;
use App\Models\Employee;
use App\Models\Exam;
use Carbon\Carbon;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamCancelTest extends TestCase
{
    use RefreshDatabase;

    protected Exam $exam;

    protected Center $center;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->center = Center::factory()->create();

        Carbon::setTestNow('2026-01-01 10:00:00');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_success_exam_cancell(): void
    {
        $this->withoutExceptionHandling();
        $actor = Employee::factory()
            ->scheduler()
            ->create([
                'center_id' => $this->center->id,
            ]);

        $exam = Exam::factory()
            ->create([
                'center_id' => $this->center->id,
                'begin_time' => Carbon::now()->addMinutes(10),
            ]);

        $response = $this->actingAs($actor)
            ->deleteJson(route('exams.destroy', ['exam' => $exam]),
                ['cancelledReason' => 'Отменен']
            );

        $response->assertNoContent();
    }

    public function test_fail_cancel_repeat(): void
    {
        $exam = new Exam([
            'cancelled_at' => '2026-01-01 10:00:00',
        ]);

        $action = new CancelExam(app(ExamCancellRules::class));
        $this->expectException(BusinessException::class);
        $action->execute($exam, 'dfds');
    }

    public function test_fail_cancel_not_pending_exam(): void
    {
        $exam = new Exam([
            'begin_time' => '2026-01-01 09:30:00',
        ]);

        $action = new CancelExam(app(ExamCancellRules::class));
        $this->expectException(BusinessException::class);
        $action->execute($exam, 'dfds');
    }
}
