<?php

namespace Tests\Feature\Employee;

use App\Models\Employee;
use Carbon\Carbon;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeDeleteTest extends TestCase
{
    use RefreshDatabase;
    protected Employee $actor;
    protected Employee $activeEmployee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->actor = Employee::factory()
            ->orgAdmin()
            ->create();
        $this->activeEmployee = Employee::factory()
            ->active()
            ->create();
        Carbon::setTestNow();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function test_success_employee_delete(): void
    {
        $this->withoutExceptionHandling();
        $response = $this->actingAs($this->actor)
            ->deleteJson(route('employees.destroy', ['employee' => $this->activeEmployee]));

        $this->assertDatabaseHas('employees', [
            'id' => $this->activeEmployee->id,
            'is_active' => false,
        ]);

        $response->assertNoContent();
    }

    public function test_fail_delete_not_active(): void
    {
        $notActiveEmployee = Employee::factory()
            ->notActive()
            ->create();

        $response = $this->actingAs($this->actor)
            ->deleteJson(route('employees.destroy', ['employee' => $notActiveEmployee]));

        $this->assertDatabaseHas('employees', [
            'id' => $notActiveEmployee->id,
            'is_active' => $notActiveEmployee->is_active,
        ]);

        $response->assertBadRequest();
    }
}
