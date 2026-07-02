<?php

namespace Tests\Feature\Report;

use App\Models\Attempt;
use App\Models\Center;
use App\Models\Employee;
use Carbon\Carbon;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrdoGenerationTest extends TestCase
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

    public function test_success_certificate(): void
    {
        Attempt::factory(5)
            ->checked()
            ->passed()->create([
                'created_at' => Carbon::now(),
                'center_id' => $this->center->id,
            ]);

        $response = $this->actingAs($this->actor)
            ->getJson(route('reports.frdo.download', [
                'type' => 'certificates',
                'examDate' => Carbon::now()->format('Y-m-d'),
            ]));

        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('Content-Type')
        );
        $response->assertStatus(200); //
    }

    public function test_success_references(): void
    {
        Attempt::factory(5)
            ->checked()
            ->failed()
            ->create([
                'created_at' => Carbon::now(),
                'center_id' => $this->center->id,
            ]);

        $response = $this->actingAs($this->actor)
            ->getJson(route('reports.frdo.download', [
                'type' => 'references',
                'examDate' => Carbon::now()->format('Y-m-d'),
            ]));

        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('Content-Type')
        );
        $response->assertStatus(200);
    }
}
