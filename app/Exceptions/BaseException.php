<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BaseException extends Exception
{
    protected int $code;
    public function __construct(string $message = '')
    {
        parent::__construct($message);
    }

    public function render(Request $request): JsonResponse|RedirectResponse
    {

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->getMessage(),
            ], $this->code);
        }

        return Inertia::flash(['error' => $this->getMessage()])->back();
    }
}
