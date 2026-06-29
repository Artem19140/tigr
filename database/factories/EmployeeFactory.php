<?php

namespace Database\Factories;

use App\Enums\EmployeeRole;
use App\Models\Center;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'surname' => fake()->randomElement(
                [
                    'Рязанова',
                    'Петрова',
                    'Привалова',
                    'Шинкевич',
                    'Майорова']
            ),
            'name' => fake()->firstNameFemale(),
            'patronymic' => fake()->randomElement(
                [
                    'Юрьевна',
                    'Сергеевна',
                    'Вячеславовна',
                    'Леонидовна',
                    'Максимовна']
            ),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'is_active' => true,
            'remember_token' => Str::random(10),
            'center_id' => Center::factory(),
            'job_title' => fake()->randomElement(['Специалист центра тестирования иностранных граждан', 'Директор центра тестирования иностранных граждан', 'Сотрудник центра тестирования иностранных граждан', 'Тестер центра тестирования иностранных граждан']),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function notActive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function withRole(EmployeeRole $role)
    {
        return $this->afterCreating(function ($employee) use ($role) {
            $role = Role::findByEnum($role);
            $employee->roles()->syncWithoutDetaching($role);
        });
    }

    public function platformAdmin()
    {
        return $this->withRole(EmployeeRole::PlatformAdmin);
    }

    public function orgAdmin()
    {
        return $this->withRole(EmployeeRole::CenterAdmin);
    }

    public function operator()
    {
        return $this->withRole(EmployeeRole::Operator);
    }

    public function director()
    {
        return $this->withRole(EmployeeRole::Director);
    }

    public function scheduler()
    {
        return $this->withRole(EmployeeRole::Scheduler);
    }

    public function examiner()
    {
        return $this->withRole(EmployeeRole::Examiner);
    }
}