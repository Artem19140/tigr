<?php

namespace Tests\Feature\Employee;

use App\Enums\EmployeeRole;
use App\Models\Employee;
use App\Models\Role;
use Carbon\Carbon;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeCreateTest extends TestCase
{
    use RefreshDatabase;
    protected $seeder = RolesSeeder::class;
    protected Role $platformAdminRole;
    protected Role $orgAdminRole;
    protected Employee $actor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->actor = Employee::factory()
            ->orgAdmin()
            ->create();

        $this->platformAdminRole = Role::findByEnum(EmployeeRole::PlatformAdmin);

        $this->orgAdminRole = Role::findByEnum(EmployeeRole::CenterAdmin);
        Carbon::setTestNow(now());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    protected function employeeBody(array $overrrides = []): array
    {
        return array_merge([
            'surname' => fake()->name(),
            'name' => fake()->name(),
            'patronymic' => fake()->name(),
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'roles' => [ Role::findByEnum(EmployeeRole::Operator)->id ],
            'email' => 'unique@bk.ru',
        ], $overrrides);
    }

    protected function postEmployee(Employee $actingAs, array $overrrides = [])
    {
        return $this->actingAs($actingAs)
            ->postJson("employees", $this->employeeBody($overrrides));
    }

    public function test_success(): void
    {
        $this->withoutExceptionHandling();

        $role = Role::findByEnum(EmployeeRole::Operator);

        $response = $this->postEmployee($this->actor, ['roles' => [$role->id]]);

        $response->assertStatus(200);
    }

    public function test_success_org_admin_creating(): void
    {
        $platformAdmin = Employee::factory()
            ->platformAdmin()
            ->create();

        $response = $this->postEmployee($platformAdmin, [
            'roles' => [ $this->orgAdminRole->id ]
        ]);
        $response->assertOk();
    }

    public function test_fail_center_admin_creating_with_no_role_platform_admin(): void
    {
        $operator = Employee::factory()
            ->operator()
            ->create();
        $response = $this->postEmployee($operator,[
            'roles' => [$this->orgAdminRole->id]
        ]);
        $response->assertStatus(403);
    }

    public function test_fail_403_platform_admin_creating(): void
    {
        $response = $this->postEmployee($this->actor, [
            'roles' => [$this->platformAdminRole->id]
        ]);
        $response->assertNotFound();
    }
}
