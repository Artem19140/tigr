<?php

namespace Tests\Feature\Report;

use App\Models\Attempt;
use App\Models\Center;
use App\Models\Employee;
use Carbon\Carbon;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlatTableGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected Employee $actor;

    protected Center $center;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->center = Center::factory()
            ->create();

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

    public function test_success_flat_table_generation(): void
    {
        Attempt::factory(5)
            ->checked()
            ->passed()
            ->create([
                'created_at' => Carbon::now(),
                'center_id' => $this->center->id,
            ]);
        $response = $this->actingAs($this->actor)
            ->get(route('reports.flat-table', [
                'dateFrom' => Carbon::now()->subDay()->format('Y-m-d'),
                'dateTo' => Carbon::now()->addDay()->format('Y-m-d'),
            ]));

        $this->assertStringContainsString(
            'text/csv',
            $response->headers->get('Content-Type')
        );

        $response->assertStatus(200);
    }
}
