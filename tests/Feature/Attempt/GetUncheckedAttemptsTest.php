<?php

namespace Tests\Feature\Attempt;

use App\Models\Attempt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetUncheckedAttemptsTest extends TestCase
{
    use RefreshDatabase;

    public function test_success_unchecked_attempts(): void
    {
        Attempt::factory(2)
            ->finished()
            ->create();
        $attempts = Attempt::query()
            ->unchecked()
            ->get();
        $this->assertNotEmpty($attempts);
    }

    public function test_success_unchecked_annulled_attempts(): void
    {
        Attempt::factory(2)
            ->finished()
            ->create([
                'annulled_at' => now(),
            ]);
        $attempts = Attempt::query()
            ->unchecked()
            ->get();
        $this->assertNotEmpty($attempts);
    }

    public function test_fail_checked_attempts(): void
    {
        Attempt::factory(2)
            ->checked()
            ->create();
        $attempts = Attempt::query()
            ->unchecked()
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
            ->unchecked()
            ->get();
        $this->assertEmpty($attempts);
    }
}
