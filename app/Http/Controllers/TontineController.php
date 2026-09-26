<?php

namespace App\Http\Controllers;

use App\Models\Tontine;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TontineController extends Controller
{
    /**
     * Affiche la liste des tontines.
     */
    public function index()
    {
        $tontines = Tontine::latest()->paginate(10);
        return view('tontines.index', compact('tontines'));
    }

    /**
     * Enregistre une nouvelle tontine.
     */
    public function store(Request $request)
    {
        // Vérifie le rôle (président ou trésorier uniquement)
        $this->authorize('create', Tontine::class);

        // Empêche la création si une tontine est déjà active
        if (Tontine::where('status', 'active')->exists()) {
            throw ValidationException::withMessages([
                'name' => 'Impossible de créer une nouvelle tontine : une tontine est déjà en cours. Clôturez-la d\'abord.',
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'rotating_amount' => ['required', 'integer', 'min:0'],
            'collective_amount' => ['required', 'integer', 'min:0'],
        ]);

        Tontine::create([
            ...$validated,
            'status' => 'active',
        ]);

        return redirect()->route('admin.tontines.index')
            ->with('success', 'La tontine a été créée avec succès.');
    }

    /**
     * Met à jour les informations d'une tontine.
     */
    public function update(Request $request, Tontine $tontine)
    {
        $this->authorize('update', $tontine);

        // Bloque toute modification si des caisses collectives ont déjà été enregistrées
        if ($tontine->collectiveContributions()->exists()) {
            throw ValidationException::withMessages([
                'name' => 'Impossible de modifier cette tontine : des cotisations de caisse collective y sont déjà associées.',
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'rotating_amount' => ['required', 'integer', 'min:0'],
            'collective_amount' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:active,closed'],
        ]);

        if (
            $validated['status'] === 'active'
            && $tontine->status !== 'active'
            && Tontine::where('status', 'active')->where('id', '!=', $tontine->id)->exists()
        ) {
            throw ValidationException::withMessages([
                'status' => 'Une autre tontine est déjà active.',
            ]);
        }

        $tontine->update($validated);

        return redirect()->route('admin.tontines.index')
            ->with('success', 'La tontine a été mise à jour.');
    }
    /**
     * Clôture une tontine.
     */
    public function close(Tontine $tontine)
    {
        $this->authorize('update', $tontine);

        $tontine->update(['status' => 'closed']);

        return back()->with('success', 'La tontine a été clôturée.');
    }

    // modification 

    public function edit(Tontine $tontine)
    {
        $this->authorize('update', $tontine);

        $hasContributions = $tontine->collectiveContributions()->exists();

        return view('tontines.edit', compact('tontine', 'hasContributions'));
    }
}
