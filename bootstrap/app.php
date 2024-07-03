<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: 'api/v1',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $exception) {
            return jsonResponse(status: 422, message: $exception->getMessage(), errors: $exception->errors());
        });
        $exceptions->render(function (AccessDeniedHttpException $exception) {
            return jsonResponse(status: 401, message: $exception->getMessage());
        });
        $exceptions->render(function (NotFoundHttpException $exception) {
            return jsonResponse(status: 404, message: $exception->getMessage());
        });
        $exceptions->render(function (AuthenticationException $exception) {
            return jsonResponse(status: 401, message: $exception->getMessage());
        });
        $exceptions->render(function (HttpException $exception) {
            return jsonResponse(status: 401, message: $exception->getMessage());
        });
    })->create();