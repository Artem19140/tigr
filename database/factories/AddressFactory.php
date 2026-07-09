<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    public function active(): AddressFactory
    {
        return $this->state(function () {
            return [
                'is_active' => true,
            ];
        });
    }

    public function notActive(): AddressFactory
    {
        return $this->state(function () {
            return [
                'is_active' => false,
            ];
        });
    }

    public function withCapacity(int $capacity): AddressFactory
    {
        return $this->state(function () use ($capacity) {
            return [
                'capacity' => $capacity,
            ];
        });
    }

    public function definition(): array
    {
        return [
            'address' => fake()->streetAddress,
            'capacity' => fake()->numberBetween(8, 20),
            'creator_id' => Employee::factory(),
        ];
    }
}
