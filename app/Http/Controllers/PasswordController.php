<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Affiche le formulaire de changement de mot de passe.
     */
    public function showChangeForm()
    {
        return view('auth.change-password');
    }

    /**
     * Enregistre le nouveau mot de passe.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],
        ]);

        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Votre mot de passe a été modifié avec succès.');
    }
}