<?php

namespace App\Http\Controllers\Web\PlatformAdmin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
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
            4 => Artisan::call('migrate:fresh --seed'),
            5 => Artisan::call('deploy'),
            
            default => Log::critical('command not found', [
                'command' => $commandNumber
            ])
        };

        Log::info('command executed', [
            'command' => $commandNumber
        ]);

        return response()->noContent();
    }
}
