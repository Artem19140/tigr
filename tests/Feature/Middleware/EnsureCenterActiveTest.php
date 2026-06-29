<?php

namespace Tests\Feature\Middleware;

use App\Models\Center;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnsureCenterActiveTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_success(): void
    {
        $center = Center::factory()
            ->create([
                'is_active' => false
            ]);

        $employee = Employee::factory()
            ->create([
                'center_id' => $center->id,
                'is_active' => true
            ]);

        $response = $this->actingAs($employee)
            ->get('/exams');
        $this->assertGuest('web');
        $response->assertRedirectToRoute('login');
    }
}
