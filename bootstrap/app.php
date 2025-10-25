<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;
use App\Http\Middleware\VerifyJwt; // ⬅️ importa tu middleware

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias para usar 'jwt' en rutas
        $middleware->alias([
            'jwt' => VerifyJwt::class,
        ]);

        // (Opcional) si querés que TODAS las rutas API lo usen:
        // $middleware->appendToGroup('api', 'jwt');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
