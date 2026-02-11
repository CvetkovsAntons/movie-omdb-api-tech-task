<?php

use App\Services\External\OMDbApi\Exceptions\OMDbApiException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $e, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        });

        $exceptions->render(function (OMDbApiException $e, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            Log::error($e);

            return response()->json(['message' => $e->getMessage()], $e->status());
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            return response()->json(['message' => 'Route not found'], 404);
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            Log::error($e);

            return response()->json(['message' => 'Internal Server Error'], 500);
        });
    })
    ->create();
