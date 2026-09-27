<?php

namespace Tests\Feature\Attempt;

use App\Models\Attempt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetUnreviewedAtAttemptsTest extends TestCase
{
    use RefreshDatabase;

    public function test_success_unreviewed_attempts(): void
    {
        Attempt::factory(2)
            ->finished()
            ->create();
        $attempts = Attempt::query()
            ->unreviewed()
            ->get();
        $this->assertNotEmpty($attempts);
    }

    public function test_success_unreviewed_annulled_attempts(): void
    {
        Attempt::factory(2)
            ->finished()
            ->create([
                'annulled_at' => now(),
            ]);
        $attempts = Attempt::query()
            ->unreviewed()
            ->get();
        $this->assertNotEmpty($attempts);
    }

    public function test_fail_reviewed_attempts(): void
    {
        Attempt::factory(2)
            ->checked()
            ->create();
        $attempts = Attempt::query()
            ->unreviewed()
            ->get();
        $this->assertEmpty($attempts);
    }

    public function test_fail_checked_annulled_attempts(): void
    {
        Attempt::factory(2)
            ->checked()
            ->create([
                'annulled_at' => now(),
            ]);
        $attempts = Attempt::query()
            ->unreviewed()
            ->get();
        $this->assertEmpty($attempts);
    }
}
