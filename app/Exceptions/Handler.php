<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $exception->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return response()->json([
                'message' => 'Resource Not Found'
            ], Response::HTTP_NOT_FOUND);
        }

        // Gérer d'autres exceptions...
        return response()->json([
            'message' => 'Server Error',
            'error' => $exception->getMessage(),
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
