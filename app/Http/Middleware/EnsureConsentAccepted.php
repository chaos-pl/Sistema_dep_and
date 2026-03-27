<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureConsentAccepted
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return $next($request);
        }

        if (
            !$request->routeIs('consentimiento.*') &&
            !$request->routeIs('logout') &&
            !$request->routeIs('logout.view') &&
            !auth()->user()->acepto_consentimiento
        ) {
            return redirect()->route('consentimiento.create');
        }

        return $next($request);
    }
}
