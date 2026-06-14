<?php

namespace Database\Seeders;

use App\Enums\CounterKey;
use App\Enums\EmployeeRole;
use App\Models\Center;
use App\Models\Counter;
use App\Models\Employee;
use App\Models\Role;
use Database\Seeders\ExamTypes\PATENT\PatentSeeder;
use Database\Seeders\ExamTypes\RVP\RvpSeeder;
use Database\Seeders\ExamTypes\VNZH\VnzhSeeder;
use Database\Seeders\Local\EmployeeSeeder;
use Database\Seeders\Local\ForeignNationalSeeder;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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

        $center = Center::firstOrCreate(
            [
                'ogrn' => '1021801503382',
                'inn' => '1833010750',
            ], 
            [
                'name' => 'Федеральное государственное бюджетное образовательное учреждение высшего образования «Удмуртский государственный университет»',
                'time_zone' => 'Europe/Samara',
                'director_fio' => 'Рязанова Анна Юрьевна',
                'certificates_issue_address' => 'Удмуртская республика, г. Ижевск, ул. Университетская, д.1',
                'ogrn' => '1021801503382',
                'inn' => '1833010750',
                'short_name' => 'ФГБОУ ВО «УдГУ»',
                'address' => 'Удмуртская Республика, г. Ижевск, улица Университетская',
                'name_genitive' => 'федеральному государственному бюджетному образовательному учреждению высшего образования «Удмуртский государственный университет»',
                'commission_chairman' => 'Иванов Иван Иванович',
            ]);

        Counter::firstOrCreate(
            [
                'key' => CounterKey::RegNum,
                'center_id' => $center->id
            ],
            [
                'key' => CounterKey::RegNum, 
                'value' => CounterKey::defaultValue(CounterKey::RegNum), 
                'center_id' => $center->id,
            ]);

        Counter::firstOrCreate(
            [
                'key' => CounterKey::Group,
                'center_id' => $center->id
            ],
            [
                'key' => CounterKey::Group,
                'value' => CounterKey::defaultValue(CounterKey::Group),
                'center_id' => $center->id,
            ]);

        $email = config('app.platform_admin.login');

        $platformAdmin = Employee::firstOrCreate(
            [
                'email' => $email
            ],
            [
                'surname' => 'Петров',
                'name' => 'Николай',
                'patronymic' => 'Дмитрович',
                'email' => $email,
                'password' => Hash::make(config('app.platform_admin.password')),
                'job_title' => 'Админ',
                'center_id' => null,// $center->id,
                'has_to_change_password' => false,
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
