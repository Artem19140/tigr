<?php

namespace Tests\Feature\Address;

use App\Models\Address;
use App\Models\Employee;
use App\Models\Exam;
use Carbon\Carbon;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->employee = Employee::factory()
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
        $address = Address::factory()->create();
        $response = $this
            ->actingAs($this->employee)
            ->patchJson(route('addresses.update', ['address' => $address]), [
                'address' => fake()->streetAddress,
                'capacity' => $address->capacity + 1,
            ]);

        $response->assertStatus(200);
    }

    public function test_fail_has_exam(): void
    {
        $address = Address::factory()
            ->has(Exam::factory(10))
            ->create();

        $oldAddress = $address->address;
        $newCapacity = $address->capacity + 1;

        $response = $this
            ->actingAs($this->employee)
            ->patchJson(route('addresses.update', ['address' => $address]), [
                'address' => fake()->streetAddress,
                'capacity' => $newCapacity,
            ]);
        $address->refresh();

        $this->assertEquals($oldAddress, $address->address);

        $this->assertEquals($newCapacity, $address->capacity);
        $response->assertOk();
    }
}
