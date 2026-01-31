<?php

use App\Helpers\ApiResponder;
use App\Http\Middleware\JwtMiddleware;
use App\Providers\ModuleRouteServiceProvider;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        ModuleRouteServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'jwt.auth' => JwtMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $throwable, $request): ?JsonResponse {
            if (! $request->expectsJson()) {
                return null;
            }

            $map = [
                ValidationException::class => 422,
                AuthenticationException::class,
                UnauthorizedHttpException::class => 401,
                AuthorizationException::class => 403,
                NotFoundHttpException::class => 404,
            ];
            $status = $map[get_class($throwable)] ?? 500;
            $errors = $throwable instanceof ValidationException ? $throwable->errors() : [];
            $message = $throwable->getMessage();

            return ApiResponder::error($message, $status, $errors);
        });
    })->create();
