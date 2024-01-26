<?php

namespace App\Exceptions;

use App\Helpers\Helper;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception $e
     * @return JsonResponse
     * @throws Throwable
     */
    public function render($request, Throwable $e): JsonResponse
    {
        if ($e instanceof UnauthorizedException) {
            return response()->json(['error' => $e->getMessage()], 403);
        }

        if ($e instanceof ValidationException) {
            $errors = collect($e->errors())->flatten();
            return Helper::getResponse([
                'status' => $e->status,
                'title' => $e->getMessage(),
                'message' => $errors->isNotEmpty() ? $errors->first() : "",
            ]);
        }

        return parent::render($request, $e);
    }
}
