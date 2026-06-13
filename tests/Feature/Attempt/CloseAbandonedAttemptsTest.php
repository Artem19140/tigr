<?php

namespace Tests\Feature\Attempt;

use App\Domain\Attempt\Action\CloseAbandonedAttemptsAction;
use App\Models\Attempt;
use App\Models\Exam;
use App\Models\ExamType;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CloseAbandonedAttemptsTest extends TestCase
{
    use RefreshDatabase;

    protected CloseAbandonedAttemptsAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = app(CloseAbandonedAttemptsAction::class);
        Carbon::setTestNow('2026-01-01 10:00:00');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_close_attempt_with_no_auto_finilizing(): void
    {
        $examType = ExamType::factory()
            ->create([
                'need_human_check' => true,
            ]);
        $exam = Exam::factory()
            ->create([
                'exam_type_id' => $examType,
            ]);

        $attempt = Attempt::factory()
            ->active()
            ->create([
                'exam_id' => $exam->id,
                'expired_at' => Carbon::now()->subSecond(),
                'last_activity_at' => Carbon::now()->subSeconds(2),
            ]);

        $this->action->execute();

        $this->assertDatabaseHas('attempts', [
            'id' => $attempt->id,
            'expired_at' => $attempt->expired_at,
            'last_activity_at' => $attempt->last_activity_at,
            'finished_at' => $attempt->last_activity_at,
            'checked_at' => null,
        ]);

        $attempt->refresh();
    }

    public function test_close_attempt_with_auto_finilizing(): void
    {
        $examType = ExamType::factory()
            ->create([
                'need_human_check' => false,
            ]);

        $exam = Exam::factory()
            ->create([
                'exam_type_id' => $examType,
            ]);

        $attempt = Attempt::factory()
            ->active()
            ->create([
                'exam_id' => $exam->id,
                'expired_at' => '2026-01-01 09:59:59',
                'last_activity_at' => '2026-01-01 09:50:00',
            ]);
        $this->action->execute();

        $this->assertDatabaseHas('attempts', [
            'id' => $attempt->id,
            'expired_at' => '2026-01-01 09:59:59',
            'last_activity_at' => '2026-01-01 09:50:00',
            'finished_at' => '2026-01-01 09:50:00',
        ]);
        $attempt->refresh();
        $this->assertNotNull($attempt->checked_at);
    }

    public function test_does_not_close_active_atempt(): void
    {
        $attempt = Attempt::factory()
            ->active()
            ->create([
                'expired_at' => Carbon::now()->addMinute(),
                'last_activity_at' => Carbon::now(),
            ]);

        $this->action->execute();
        $this->assertDatabaseHas('attempts', [
            'id' => $attempt->id,
        ]);

        $attempt->refresh();

        $this->assertNull($attempt->finished_at);
        $this->assertNull($attempt->checked_at);
    }

    public function test_does_not_close_annulled_attempt(): void
    {
        $attempt = Attempt::factory()
            ->annulled()
            ->create([
                'expired_at' => Carbon::now()->addMinute(),
                'last_activity_at' => Carbon::now(),
            ]);

        $oldAnnulledAt = $attempt->annulled_at;
        $oldFinidhedAt = $attempt->finished_at;
        $oldCheckedAt = $attempt->checked_at;

        $this->action->execute();

        $attempt->refresh();

        $this->assertEquals($oldAnnulledAt, $attempt->annulled_at);
        $this->assertEquals($oldFinidhedAt, $attempt->finished_at);
        $this->assertEquals($oldCheckedAt, $attempt->checked_at);
    }
}
