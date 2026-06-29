<?php

namespace Tests\Feature\Report;

use App\Models\Attempt;
use App\Models\Center;
use App\Models\Employee;
use Carbon\Carbon;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class FrdoGenerationAvailableTest extends TestCase
{
    use RefreshDatabase;

    protected Employee $actor;

    protected Center $center;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->center = Center::factory()->create();
        $this->actor = Employee::factory()
            ->director()
            ->create([
                'center_id' => $this->center->id,
            ]);

        Carbon::setTestNow(now());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    protected function getFrdo(string $type): TestResponse
    {
        return $this->actingAs($this->actor)
            ->getJson(route('reports.frdo.available', [
                'type' => $type,
                'examDate' => Carbon::now()->format('Y-m-d'),
            ]));
    }

    public function test_success_passed(): void
    {
        Attempt::factory(5)->checked()->passed()->create([
            'center_id' => $this->center->id,
        ]);
        $response = $this->getFrdo('certificates');

        $response->assertOk();
    }

    public function test_success_not_passed(): void
    {
        Attempt::factory(5)
            ->checked()
            ->failed()
            ->create([
                'center_id' => $this->center->id,
            ]);

        Attempt::factory(5)
            ->checked()
            ->passed()
            ->create([
                'center_id' => $this->center->id,
            ]);
        $response = $this->getFrdo('references');

        $response->assertOk();
    }

    public function test_fail_no_attempts(): void
    {
        $response = $response = $this->getFrdo('certificates');

        $response->assertBadRequest();
    }

    public function test_fail_not_checked_attempts(): void
    {
        Attempt::factory(5)
            ->passed()
            ->create([
                'center_id' => $this->center->id,
            ]);
        $response = $response = $this->getFrdo('certificates');

        $response->assertBadRequest();
    }

    public function test_fail_no_attempts_for_success(): void
    {
        Attempt::factory(5)
            ->checked()
            ->failed()
            ->create([
                'created_at' => Carbon::now(),
            ]);
        $response = $response = $this->getFrdo('certificates');

        $response->assertBadRequest();
    }

    public function test_fail_no_attempts_for_fail(): void
    {
        Attempt::factory(5)
            ->checked()
            ->passed()
            ->create([
                'center_id' => $this->center->id,
            ]);
        $response = $response = $this->getFrdo('references');

        $response->assertBadRequest();
    }

    public function test_fail_has_active_attempts(): void
    {
        Attempt::factory(5)
            ->active()
            ->passed()
            ->create([
                'center_id' => $this->center->id,
            ]);
        $response = $response = $this->getFrdo('references');

        $response->assertBadRequest();
    }
}
