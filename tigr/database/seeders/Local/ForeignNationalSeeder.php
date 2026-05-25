<?php

namespace Database\Seeders\Local;

use App\Models\Center;
use App\Models\ForeignNational;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ForeignNationalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    use WithoutModelEvents;

    public function run(): void
    {
        $center = Center::firstWhere('inn', '1833010750');
        ForeignNational::factory(500)
            ->withRandomCreator()
            ->create([
                'center_id' => $center->id,
            ]);
    }
}
