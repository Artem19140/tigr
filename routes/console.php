<?php

use App\Domain\Attempt\Action\CloseAbandonedAttemptsAction;
use App\Domain\Exam\Action\ClearExpiredExamCodesAction;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;

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
    $process = new Process('cd /var/www/tigr && /var/www/tigr/up.bash >> /var/www/tigr/git.log 2>&1');
    $process->run();
})->purpose('deploy project:git pull, update dependences, recache');