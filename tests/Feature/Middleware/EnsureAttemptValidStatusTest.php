<?php

namespace Tests\Feature\Middleware;

use App\Models\Attempt;
use App\Models\ForeignNational;
use App\Support\AppMiddleware;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class EnsureAttemptValidStatusTest extends TestCase
{
    use RefreshDatabase;
    protected ForeignNational $actor;
    protected function setUp(): void
    {
        parent::setUp();
        $this->actor = ForeignNational::factory()
            ->create();
        Route::get('/_test/attempt/{attempt}', function (Attempt $attempt) {
            return response()->json(['ok' => true]);
        })->middleware(
            SubstituteBindings::class,
            AppMiddleware::ENSURE_ATTEMPT_VALID_STATUS
        );
        Carbon::setTestNow('2026-01-01 10:00:00');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    protected function getAttempt(Attempt $attempt): TestResponse
    {
        return $this->actingAs($this->actor)
            ->getJson("/_test/attempt/{$attempt->id}");
    }

    public function test_success_redirect(): void
    {
        $this->withoutExceptionHandling();
        $attempt = Attempt::factory()
            ->active()
            ->create([
                'foreign_national_id' => $this->actor->id,
                'expired_at' => '2026-01-01 10:00:01',
                'started_at' => '2026-01-01 09:00:01',
            ]);

        $this->getAttempt($attempt)
            ->assertOk();
    }

    public function test_redirect_finished_attempt(): void
    {
        $attempt = Attempt::factory()
            ->finished()
            ->create([
                'foreign_national_id' => $this->actor->id,
                'finished_at' => '2026-01-01 09:50:00',
            ]);
        $this->getAttempt($attempt)
            ->assertUnauthorized();
    }

    public function test_redirect_annulled_attempt(): void
    {
        $attempt = Attempt::factory()
            ->annulled()
            ->create([
                'foreign_national_id' => $this->actor->id,
                'annulled_at' => '2026-01-01 09:50:00',
            ]);

        $this->getAttempt($attempt)
            ->assertUnauthorized();
    }

    public function test_redirect_expired(): void
    {
        $attempt = Attempt::factory()
            ->active()
            ->create([
                'foreign_national_id' => $this->actor->id,
                'expired_at' => '2026-01-01 09:59:59',
            ]);

        $this->getAttempt($attempt)
            ->assertUnauthorized();
    }
}
