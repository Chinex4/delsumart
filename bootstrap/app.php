<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Http\Middleware\VerifiedStudentMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php', commands: __DIR__.'/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(SecurityHeadersMiddleware::class);
        $middleware->alias(['admin' => AdminMiddleware::class, 'verified.student' => VerifiedStudentMiddleware::class]);
    })
    ->withExceptions(fn (Exceptions $exceptions) => null)->create();
