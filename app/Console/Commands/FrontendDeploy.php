<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

#[Signature('app:frontend-deploy')]
#[Description('frontend deploy')]
class FrontendDeploy extends Command
{
    public function handle()
    {
        $path = '/var/www/tigr';

        $commands = [
            ['npm', 'ci'],
            ['node', 'node_modules/.bin/vite', 'build']
        ];

        $this->info("Выполняем frontend deploy...");
        Log::info("Выполняем frontend deploy...");

        foreach($commands as $command){
            $commandImploded = implode(' ', $command);

            $process = new Process($command ,$path);
            $process->run();

            if (!$process->isSuccessful()) {
                $this->error("npm ($commandImploded)" . $process->getErrorOutput());
                Log::warning("npm ($commandImploded)" . $process->getErrorOutput());
                return 1;
            }

            $this->info($process->getOutput());
            Log::warning("npm ($commandImploded)" . $process->getOutput());
            
        }
        $this->info('npm команты успешно завершены');
    }
}
