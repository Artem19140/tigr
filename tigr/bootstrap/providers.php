<?php

use App\Providers\AppServiceProvider;
use App\Providers\AuthorizationServiceProvider;
use App\Providers\ObservabilityServiceProvider;

return [
    AppServiceProvider::class,
    AuthorizationServiceProvider::class,
    ObservabilityServiceProvider::class,
];
