<?php

namespace Database\Seeders;

use App\Enums\CounterKey;
use App\Enums\EmployeeRole;
use App\Models\Counter;
use App\Models\Employee;
use App\Models\Role;
use Database\Seeders\ExamTypes\PATENT\PatentSeeder;
use Database\Seeders\ExamTypes\RVP\RvpSeeder;
use Database\Seeders\ExamTypes\VNZH\VnzhSeeder;
use Database\Seeders\Local\EmployeeSeeder;
use Database\Seeders\Local\ForeignNationalSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            PatentSeeder::class,
            RvpSeeder::class,
            VnzhSeeder::class,
        ]);
        
        $counters = CounterKey::cases();

        foreach($counters as $counter){
            Counter::firstOrCreate(
            [
                'key' => $counter
            ],
            [
                'key' => $counter,
                'value' => $counter->defaultValue()
            ]);
        }

        $email = config('app.platform_admin.email');
        $password = config('app.platform_admin.password');
        
        if (blank($email) || blank($password)) {
            throw new \RuntimeException(
                'Platform admin credentials are not configured.'
            );
        }
       
        $platformAdmin = Employee::updateOrCreate(
            [
                'email' => $email
            ],
            [
                'surname' => 'Петров',
                'name' => 'Николай',
                'patronymic' => 'Дмитрович',
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now()
            ]);
        
        $platformAdminRole = Role::findByEnum(EmployeeRole::PlatformAdmin);

        $platformAdmin->roles()->syncWithoutDetaching([$platformAdminRole->id]);
        
        $this->call([
            EmployeeSeeder::class
        ]);

        if (! app()->isProduction()) {
            $this->call([
                EmployeeSeeder::class,
                ForeignNationalSeeder::class,
            ]);
        }
    }
}
