<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->trustProxies(at: '*');

        $middleware->web(
            append: [
                \App\Http\Middleware\SecurityHeaders::class,
            ],
            replace: [
                \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class => \App\Http\Middleware\ValidateCsrfToken::class,
            ],
        );

        // ⭐ $middleware ke sath alias register karna bilkul perfect hai
        $middleware->alias([
            'admin'              => \App\Http\Middleware\AdminMiddleware::class,
            'employee'           => \App\Http\Middleware\EmployeeMiddleware::class,
            'super_admin'        => \App\Http\Middleware\SuperAdminMiddleware::class,
            'check_subscription' => \App\Http\Middleware\CheckSubscription::class,
            'shadow_mode'        => \App\Http\Middleware\ShadowMode::class,
            'shadow_audit'       => \App\Http\Middleware\ShadowAuditMiddleware::class,
            'partner'            => \App\Http\Middleware\PartnerMiddleware::class,
            'feature'            => \App\Http\Middleware\CheckFeature::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();