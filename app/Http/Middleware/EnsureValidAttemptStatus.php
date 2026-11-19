<?php

namespace App\Http\Middleware;

use App\Enums\BusinessCode;
use App\Modules\Attempt\ActiveAttemptRules;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class EnsureValidAttemptStatus
{
    public function __construct(
        protected ActiveAttemptRules $rules
    ){}
    
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $attempt = $request->route('attempt');

        $result = $this->rules->check($attempt);

        if($result->isNotAvailable()){

            $this->log(
                $result->code() ?? '',
                $attempt->id
            );

            return $this->deny(
                $result->message() ?? '',
                $request
            );
        }

        return $next($request);
    }

    protected function deny(
        string $message,
        Request $request
    ): JsonResponse|RedirectResponse {
        
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
            ], 400);
        }

        Inertia::flash(['error' => $message]);
        Auth::logout();

        return redirect()->route('login');
    }

    protected function log(
        BusinessCode $code,
        int $attemptId
    ){
        Log::info($code->value, [
            'attempt_id' => $attemptId
        ]);
    }
}
