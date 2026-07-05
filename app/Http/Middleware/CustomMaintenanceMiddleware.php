<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;
use Closure;

class CustomMaintenanceMiddleware extends Middleware
{
    public function handle($request, Closure $next)
    {
        // Always allow API routes and /irc2026 bypass route
        if ($request->is('irc2026') || $request->is('api/*') || $request->is('api/v1/*')) {
            return $next($request);
        }

        // Always allow if bypass cookie is set
        if ($request->cookie('laravel_maintenance') === 'irc2026') {
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
