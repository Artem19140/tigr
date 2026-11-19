<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\ExamTypes\PATENT\PatentSeeder;
use Database\Seeders\ExamTypes\RVP\RvpSeeder;
use Database\Seeders\ExamTypes\VNZH\VnzhSeeder;

class ExamTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            PatentSeeder::class,
            RvpSeeder::class,
            VnzhSeeder::class,
        ]);
    }
}