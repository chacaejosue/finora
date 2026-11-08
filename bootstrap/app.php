<?php

use App\Http\Middleware\EnsureClienteRole;
use App\Http\Middleware\EnsureConsultorRole;
use App\Http\Middleware\EnsureOperativoRole;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('finora:actualizar-tipo-cambio')
            ->dailyAt('00:05')
            ->timezone('America/La_Paz')
            ->withoutOverlapping(30);
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectTo(
            guests: '/login',
            users: function (Request $request) {
                if ($request->user()?->rol === 'CLIENTE') {
                    return '/mi-cuenta';
                }
                if ($request->user()?->rol === 'COLABORADOR') {
                    return '/panel/colaborador';
                }

                return '/panel';
            }
        );
        $middleware->alias([
            'rol.consultor' => EnsureConsultorRole::class,
            'rol.operativo' => EnsureOperativoRole::class,
            'rol.cliente' => EnsureClienteRole::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'productos/*/like',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
