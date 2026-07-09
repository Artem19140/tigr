<?php

namespace Tests\Feature\Address;

use App\Models\Address;
use App\Models\Employee;
use Carbon\Carbon;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressDeleteTest extends TestCase
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
        $address = Address::factory()->active()->create();
        $response = $this
            ->actingAs($this->employee)
            ->deleteJson(route('addresses.destroy', ['address' => $address]));
        $response->assertNoContent();
        $address->refresh();
        $this->assertFalse($address->is_active);
    }
}
