<?php

namespace Tests\Feature\Address;

use App\Models\Employee;
use Carbon\Carbon;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressCreateTest extends TestCase
{
    use RefreshDatabase;
    protected Employee $actor;
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->actor = Employee::factory()
            ->orgAdmin()
            ->create();

        Carbon::setTestNow(
            Carbon::now()
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_success(): void
    {
        $response = $this
            ->actingAs($this->actor)
            ->postJson(route('addresses.store'), [
                'address' => fake()->streetAddress,
                'capacity' => 12,
            ]);

        $response->assertStatus(201);
    }

    public function test_fail_less_zero_capacity(): void
    {
        $response = $this
            ->actingAs($this->actor)
            ->postJson(route('addresses.store'), [
                'address' => fake()->streetAddress,
                'capacity' => -12
            ]);

        $response->assertUnprocessable();
    }
}
