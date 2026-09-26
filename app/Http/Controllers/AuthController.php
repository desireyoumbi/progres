<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Affiche le formulaire de connexion.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Traitement de la connexion par téléphone et mot de passe.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirection vers le dashboard unifié (la vue gérera l'affichage selon le rôle)
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'phone' => 'Numéro de téléphone ou mot de passe incorrect.',
        ])->onlyInput('phone');
    }

    /**
     * Déconnexion de la plateforme.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
