<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

#[Signature('app:git-pull')]
#[Description('Command description')]
class GitPull extends Command
{
    protected $signature = 'git:pull';
    protected $description = 'git pull';
    public function handle()
    {
        $path = '/var/www/tigr';
        $this->info("git pull...");
        Log::info("git pull...");
        $process = new Process(['git', 'pull'], $path);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error("Git pull failed: " . $process->getErrorOutput());
            Log::warning("Git pull failed: " . $process->getErrorOutput());
            return 1;
        }

        $this->info($process->getOutput());
        
        Log::info('изменения получены');
        $this->info('изменения получены');
    }
}
