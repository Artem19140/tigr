<?php

namespace App\Http\Controllers\Web\PlatformAdmin;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CommandsController
{
    public function execute(Request $request)
    {
        $request->validate([
            'command' => ['required', 'integer']
        ]);

        $commandNumber = $request->input('command');
        match($commandNumber){
            1 => Artisan::call('migrate --force'),
            2 => Artisan::call('optimize:clear'),
            3 => Artisan::call('optimize'),
            4 => Artisan::call('migrate:fresh --seed --force'),
            5 => Artisan::call('deploy'),
            
            default => Log::critical('command not found', [
                'command' => $commandNumber
            ])
        };

        Log::info('command executed', [
            'command' => $commandNumber
        ]);
        Employee::firstOrCreate(
                [
                    'email' =>"1@udsu.ru"
                ],    
                [
                    'surname' => 'f',
                    'name' => 'n',
                    'patronymic'=> 'o',
                    'job_title' => '3s',
                    'center_id' => 1,
                    'email' => "1@udsu.ru",
                    'has_to_change_password' => false,
                    'password' =>Hash::make('12345678'),
                ]);

        return response()->noContent();
    }
}
