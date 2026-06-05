<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CommandsController
{
    public function execute(Request $request){
        $request->validate([
            'command' => ['required', 'integer']
        ]);

        $commandNumber = $request->input('command');

        match($commandNumber){
            1 => Artisan::call('migrate --force'),
            2 => Artisan::call('optimize:clear'),
            3 => Artisan::call('optimize'),
            4 => Artisan::call('migrate:fresh --seed')
        };

        Log::info('command executed', [
            'command' => $commandNumber
        ]);
        
        return response()->noContent();
    }
}
