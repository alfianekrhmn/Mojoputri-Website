<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
->withRouting(
    web: __DIR__.'/../routes/web.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
    then: function () {
        // Ini akan memaksa redirect default ke dashboard admin
    },
)
->withMiddleware(function (Middleware $middleware) {
    $middleware->redirectTo(
        guests: '/login',
        users: '/admin/dashboard', // Tambahkan ini agar otomatis ke dashboard
    );
})
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
