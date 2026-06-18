<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BaseException extends Exception
{
    
    public function __construct(
        string $message = '', 
        // protected int $statusCode = 400,
        // protected string $businessCode = ''
    )
    {
        parent::__construct($message);
    }

    public function render(Request $request): JsonResponse|RedirectResponse
    {

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->getMessage(),
            ], 400);
        }

        return Inertia::flash(['error' => $this->getMessage()])->back();
    }
}
