<?php

use App\Http\Middleware\AdminAuth;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.auth' => AdminAuth::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('admin.login'));

        // Disable CSRF validation in the test suite
        if (app()->runningUnitTests()) {
            $middleware->web(remove: [\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class]);
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if (!$request->expectsJson()) {
                return redirect()->route('admin.login');
            }
        });
    })->create();
