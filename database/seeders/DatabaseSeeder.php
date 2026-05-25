<?php

namespace Database\Seeders;

use App\Enums\CounterKey;
use App\Models\Center;
use App\Models\Counter;
use App\Models\Employee;
use Carbon\Carbon;
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

        $center = Center::create([
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

        Counter::create([
            'key' => CounterKey::RegNum,
            'value' => Carbon::now()->format('y').'0000',
            'center_id' => $center->id,
        ]);

        Counter::create([
            'key' => CounterKey::Group,
            'value' => 0,
            'center_id' => $center->id,
        ]);

        Employee::factory()
            ->superAdmin()
            ->create([
                'surname' => 'Петров',
                'name' => 'Николай',
                'patronymic' => 'Дмитрович',
                'email' => env('SUPER_ADMIN_LOGIN'),
                'password' => Hash::make(env('SUPER_ADMIN_PASSWORD')),
                'job_title' => 'Админ',
                'center_id' => 1,
                'has_to_change_password' => false,
            ]);

        if (! app()->isProduction()) {
            $this->call([
                EmployeeSeeder::class,
                ForeignNationalSeeder::class,
            ]);
        }
    }
}
