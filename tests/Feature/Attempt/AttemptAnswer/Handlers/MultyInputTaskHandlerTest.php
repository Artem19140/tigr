<?php

namespace Tests\Feature\Attempt\AttemptAnswer\Handlers;

use App\Modules\AttemptAnswer\Handlers\MultyInputTaskHandler;
use App\Exceptions\Attempt\AttemptAnswerValidationException;
use App\Models\Answer;
use App\Models\AttemptAnswer;
use App\Models\Task;
use App\Models\TaskVariant;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class MultyInputTaskHandlerTest extends TestCase
{
    use RefreshDatabase;

    protected MultyInputTaskHandler $handler;

    protected TaskVariant $taskVariant;

    protected Task $task;

    protected AttemptAnswer $attemptAnswer;

    protected Answer $answer;

    protected int $mark = 1;

    protected function setUp(): void
    {
        parent::setUp();
        $task = new Task;

        $task->mark = $this->mark;

        $this->taskVariant = new TaskVariant(['id' => 1]);

        $this->taskVariant->setRelation('task', $task);

        $this->answer = new Answer([
            'task_variant_id' => $this->taskVariant->id,
            'id' => 1,
            'content' => $this->content(),
        ]);

        $this->answer->setRelation(
            'taskVariant',
            collect([$this->taskVariant])
        );

        $this->taskVariant->setRelation(
            'answers',
            collect([$this->answer])
        );

        $this->attemptAnswer = new AttemptAnswer(['id' => 1]);

        $this->attemptAnswer->setRelation('taskVariant', $this->taskVariant);

        $this->handler = app(MultyInputTaskHandler::class);

        Carbon::setTestNow(Carbon::now());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_success_validation_multi_task_handler(): void
    {
        $this->handler->validate(
            $this->content(),
            $this->attemptAnswer
        );
        $this->assertTrue(true);
    }

    public function test_fail_validation_multi_task_handler(): void
    {
        $this->expectException(AttemptAnswerValidationException::class);
        $this->handler->validate(
            $this->content(['input_3' => '']),
            $this->attemptAnswer
        );
        $this->hasLog();
    }

    protected function content(array $overrides = []): array
    {
        return [
            'input_1' => '',
            'input_2' => '',
            ...$overrides,
        ];
    }

    protected function hasLog()
    {
        Log::spy();
        Log::shouldHaveReceived('channel')
            ->with('single')
            ->once();
    }
}
