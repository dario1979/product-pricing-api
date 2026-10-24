<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Domain\Catalog\Exceptions\ProductNotFound;
use Illuminate\Http\Request;
use App\Domain\Catalog\Exceptions\ProductPriceAlreadyExists;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: '',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(
            function (ProductNotFound $exception, Request $request) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => $exception->getMessage(),
                    ], 404);
                }

                return null;
            }

        );

        $exceptions->render(
            function (ProductPriceAlreadyExists $exception) {
                return response()->json([
                    'message' => $exception->getMessage(),
                ], 409);
            }
        );
    })->create();
