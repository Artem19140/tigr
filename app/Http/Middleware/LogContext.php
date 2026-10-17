<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Symfony\Component\HttpFoundation\Response;

class LogContext
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Context::add([
            'actor_id' => $request->user()?->id,
            'ip' => $request->ip(),
            'client_ip' => $request->getClientIp(),
            'user_agent' => request()->userAgent(),
            'actor_type' => $request->user() ? $request->user()->getMorphClass() : null,
            'url' => $request->url()
        ]);

        return $next($request);
    }
}
