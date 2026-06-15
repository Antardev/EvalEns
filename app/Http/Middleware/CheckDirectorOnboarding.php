<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDirectorOnboarding
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isDirecteur()) {
            return $next($request);
        }

        $university = $user->university;

        // Pas d'université soumise → inscription obligatoire
        if (! $university) {
            return redirect()->route('director.register-university');
        }

        // Université en attente ou rejetée → page d'attente
        if (! $university->isActive()) {
            return redirect()->route('director.pending');
        }

        return $next($request);
    }
}
