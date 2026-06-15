<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsGestionnaire
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isGestionnaire()) {
            abort(403, 'Accès réservé aux gestionnaires d\'annexe.');
        }

        if (! $user->annexe_id) {
            abort(403, 'Aucune annexe n\'est associée à votre compte. Contactez l\'administrateur.');
        }

        return $next($request);
    }
}
