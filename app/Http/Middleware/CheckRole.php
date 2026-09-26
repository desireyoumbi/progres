<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Gère une requête entrante.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Rôles autorisés (ex: president, secretaire, admin)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Vérifie si l'utilisateur est connecté et si son rôle fait partie des rôles autorisés
        if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
            // S'il n'a pas le droit, on le redirige vers le tableau de bord avec une erreur
            return redirect()->route('dashboard')->with('error', "Vous n'avez pas les autorisations nécessaires pour accéder à cette page.");
        }

        return $next($request);
    }
}