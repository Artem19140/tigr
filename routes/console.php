<?php

use App\Domain\Attempt\Action\CloseAbandonedAttemptsAction;
use App\Domain\Exam\Action\ClearExpiredExamCodesAction;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    app(ClearExpiredExamCodesAction::class)->execute();
})->monthly();

Schedule::call(function () {
    app(CloseAbandonedAttemptsAction::class)->execute();
})->everyFifteenMinutes();

Artisan::command('deploy', function(){
    $result = Illuminate\Support\Facades\Process::run('cd /var/www/tigr && /var/www/tigr/up.bash >> /var/www/tigr/git.log 2>&1');
})->purpose('deploy project:git pull, update dependences, recache');

// $path = '/var/www/tigr';

// Artisan::command('git pull', function() use( $path ){
//     $process = new Process(['git', 'pull'], $path);
//     $process->run();
// })->purpose('git pull');

// Artisan::command('fronted deploy', function()use( $path ){
//     $commands = [
//         ['npm', 'ci'],
//         ['npm', 'run', 'build']
//     ];

//     foreach($commands as $command){
//         $process = new Process($command ,$path);
//         $process->run();
//     }
//     $this->info();
    
// })->purpose('fronted deploy');
