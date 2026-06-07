<?php

namespace Tests\Feature\Attempt;

use App\Models\Attempt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetPassedAndFailedAttemptsTest extends TestCase
{
    use RefreshDatabase;
    public function test_success_passed_attempts(): void
    {
        Attempt::factory(2)
            ->checked()
            ->passed()
            ->create();

        Attempt::factory()
            ->passed()
            ->banned();

        $attempts = Attempt::query()
            ->passed()
            ->get();
        $this->assertCount(2, $attempts);
    }

    public function test_success_failed_attempts(): void
    {
        Attempt::factory(2)
            ->checked()
            ->failed()
            ->create();
            
        Attempt::factory()
            ->passed()
            ->banned()
            ->create();

        $attempts = Attempt::query()
            ->failed()
            ->get();

        $this->assertCount(3, $attempts);
    }


}
