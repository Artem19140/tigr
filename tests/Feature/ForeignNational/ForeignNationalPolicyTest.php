<?php

namespace Tests\Feature\ForeignNational;

use App\Models\Employee;
use App\Models\ForeignNational;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForeignNationalPolicyTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
    }

    public function test_operator_base_access_permissions(): void
    {
        $operator = Employee::factory()->operator()->create();
        $foriegnNational = ForeignNational::factory()->create();
        $this->assertTrue($operator->can('viewAny', ForeignNational::class),
            'opearator should viewAny FN');
        $this->assertTrue($operator->can('create', ForeignNational::class),
            'opearator create viewAny FN');
        $this->assertTrue($operator->can('update', $foriegnNational),
            'opearator update viewAny FN');
        $this->assertFalse($operator->can('export', ForeignNational::class),
            'opearator should not export FN');
    }

    public function test_export_access_permissions(): void
    {
        $director = Employee::factory()->director()->create();
        
        $this->assertTrue($director->can('export', ForeignNational::class),
            'opearator should viewAny FN');

    }
}
