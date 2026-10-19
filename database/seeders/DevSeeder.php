<?php

namespace Database\Seeders;

use App\Models\Address;
use Database\Seeders\Local\EmployeeSeeder;
use Database\Seeders\Local\ForeignNationalSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DevSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            ForeignNationalSeeder::class,
            EmployeeSeeder::class,
        ]);

        Address::factory()->create();
    }
}
