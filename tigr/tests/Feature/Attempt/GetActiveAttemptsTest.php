<?php

namespace Tests\Feature\Attempt;

use App\Models\Attempt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetActiveAttemptsTest extends TestCase
{
    use RefreshDatabase;

    public function test_success_active_attempts(): void
    {
        Attempt::factory()
            ->active()
            ->create();
        $attempts = Attempt::query()
            ->active()
            ->get();
        $this->assertNotEmpty($attempts);
    }

    public function test_fail_finished_attempts(): void
    {
        Attempt::factory()
            ->finished()
            ->create();
        $attempts = Attempt::query()
            ->active()
            ->get();
        $this->assertEmpty($attempts);
    }

    public function test_fail_banned_attempts(): void
    {
        Attempt::factory()
            ->banned()
            ->create();
        $attempts = Attempt::query()
            ->active()
            ->get();
        $this->assertEmpty($attempts);
    }
}
