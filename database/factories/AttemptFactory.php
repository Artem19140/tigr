<?php

namespace Database\Factories;

use App\Models\Attempt;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ForeignNational;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attempt>
 */
class AttemptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'foreign_national_id' => ForeignNational::factory(),
            'exam_id' => Exam::factory(),
            'total_mark' => fake()->numberBetween(5, 20),
            'enrollment_id' => Enrollment::factory(),
            'created_at' => now(),
        ];
    }

    public function active()
    {
        return $this->state(function () {
            return [
                'started_at' => now(),
                'last_activity_at' => now(),
            ];
        });
    }

    public function finished()
    {
        return $this->state(function () {
            return [
                'started_at' => now(),
                'last_activity_at' => now(),
                'finished_at' => now(),
            ];
        });
    }

    public function checked()
    {
        return $this->state(function () {
            return [
                'started_at' => now(),
                'last_activity_at' => now(),
                'finished_at' => now(),
                'checked_at' => now(),
            ];
        });
    }

    public function passed()
    {
        return $this->state(function () {
            return [
                'is_passed' => true,
                'total_mark' => fake()->numberBetween(19, 22),
            ];
        });
    }

    public function failed()
    {
        return $this->state(function () {
            return [
                'is_passed' => false,
                'total_mark' => fake()->numberBetween(0, 10),
            ];
        });
    }

    public function annulled()
    {
        return $this->state(function () {
            return [
                'started_at' => now(),
                'last_activity_at' => now(),
                'annulled_at' => now(),
                'finished_at' => now(),
                'checked_at' => now(),
            ];
        });
    }
}
