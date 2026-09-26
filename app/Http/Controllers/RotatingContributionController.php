<?php

namespace App\Http\Controllers;

use App\Models\RotatingContribution;
use App\Models\Tontine;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\CarbonPeriod;

class RotatingContributionController extends Controller
{
    /**
     * Affiche la liste des cotisations de la caisse rotative (avec filtres optionnels).
     */
   
public function index(Request $request)
{
    $tontines = Tontine::all();

    $activeTontine = Tontine::where('status', 'active')->first();

    $isTreasurer = auth()->user()->role === 'treasurer';

    $users = User::all();

    $weeklyContributions = collect();

    if ($activeTontine) {

        /*
         * Tous les utilisateurs sont considérés comme membres.
         */
        $members = $users;

        /*
         * Toutes les cotisations du cycle actif.
         */
        $contributions = $activeTontine->rotatingContributions()
            ->with(['user', 'approvedBy'])
            ->get()
            ->groupBy(function ($contribution) {
                return $contribution->week->format('Y-m-d');
            });

        /*
         * Génération des semaines du cycle.
         */
        $period = CarbonPeriod::create(
            $activeTontine->start_date->copy()->startOfWeek(),
            '1 week',
            $activeTontine->end_date
        );

        foreach ($period as $date) {

            $week = $date->format('Y-m-d');

            $weekEnd = $date->copy()->endOfWeek();

            /*
             * Cotisations enregistrées pour cette semaine.
             */
            $weekContributions = $contributions->get(
                $week,
                collect()
            );

            /*
             * Indexation par user_id.
             */
            $contributionsByUser = $weekContributions->keyBy(
                'user_id'
            );

            /*
             * Construction de la liste de tous les membres.
             */
            $membersStatus = $members->map(
                function ($user) use ($contributionsByUser) {

                    $contribution =
                        $contributionsByUser->get($user->id);

                    if (!$contribution) {

                        $status = 'unpaid';

                    } elseif ($contribution->status === 'approved') {

                        $status = 'paid';

                    } elseif ($contribution->status === 'pending') {

                        $status = 'pending';

                    } else {

                        $status = 'rejected';
                    }

                    return [
                        'user' => $user,
                        'contribution' => $contribution,
                        'status' => $status,
                    ];
                }
            );

            /*
             * Statistiques de la semaine.
             */
            $paidCount = $membersStatus
                ->where('status', 'paid')
                ->count();

            $pendingCount = $membersStatus
                ->where('status', 'pending')
                ->count();

            $rejectedCount = $membersStatus
                ->where('status', 'rejected')
                ->count();

            $unpaidCount = $membersStatus
                ->where('status', 'unpaid')
                ->count();

            $totalPaid = $membersStatus
                ->where('status', 'paid')
                ->sum(function ($item) {
                    return $item['contribution']->amount ?? 0;
                });

            $totalPending = $membersStatus
                ->where('status', 'pending')
                ->sum(function ($item) {
                    return $item['contribution']->amount ?? 0;
                });

            $totalExpected =
                $members->count()
                * $activeTontine->rotating_amount;

            $weeklyContributions->push([

                'week' => $week,

                'label' =>
                    'Semaine du ' .
                    $date->format('d/m/Y') .
                    ' au ' .
                    $weekEnd->format('d/m/Y'),

                'members' => $membersStatus,

                'member_count' =>
                    $members->count(),

                'paid_count' =>
                    $paidCount,

                'pending_count' =>
                    $pendingCount,

                'rejected_count' =>
                    $rejectedCount,

                'unpaid_count' =>
                    $unpaidCount,

                'total_paid' =>
                    $totalPaid,

                'total_pending' =>
                    $totalPending,

                'total_expected' =>
                    $totalExpected,
            ]);
        }
    }

    /*
     * Récapitulatif personnel.
     */
    $myTotalPaid = 0;

    $myExpectedTotal = 0;

    if ($activeTontine) {

        $myTotalPaid = RotatingContribution::where(
            'tontine_id',
            $activeTontine->id
        )
            ->where(
                'user_id',
                auth()->id()
            )
            ->where(
                'status',
                'approved'
            )
            ->sum('amount');

        $today = now();

        $end = $activeTontine->end_date->min($today);

        $weeksElapsed = max(
            1,
            $activeTontine->start_date
                ->diffInWeeks($end) + 1
        );

        $myExpectedTotal =
            $weeksElapsed
            * $activeTontine->rotating_amount;
    }

    return view(
        'admin.rotating_contributions.index',
        compact(
            'tontines',
            'activeTontine',
            'isTreasurer',
            'users',
            'weeklyContributions',
            'myTotalPaid',
            'myExpectedTotal'
        )
    );
}


    /**
     * Enregistre une nouvelle cotisation pour la caisse rotative.
     */
    public function store(Request $request)
    {
        $activeTontine = Tontine::where('status', 'active')->first();

        if (!$activeTontine) {
            return back()->with('error', 'Aucune tontine active pour le moment. Impossible d\'enregistrer un versement.');
        }

        $isTreasurer = auth()->user()->role === 'treasurer';

        $rules = [
            'week'       => 'required|date',
            'proof_path' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        if ($isTreasurer) {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);

        $userId = ($isTreasurer && $request->filled('user_id'))
            ? $request->input('user_id')
            : auth()->id();

        // Auto-validation : le trésorier qui enregistre SA PROPRE cotisation est directement approuvé
        $isSelfTreasurerEntry = $isTreasurer && (int) $userId === (int) auth()->id();

        $data = [
            'tontine_id'  => $activeTontine->id,
            'user_id'     => $userId,
            'week'        => $validated['week'],
            'amount'      => $activeTontine->rotating_amount, // montant imposé par la tontine, pas par le formulaire
            'status'      => $isSelfTreasurerEntry ? 'approved' : 'pending',
            'approved_by' => $isSelfTreasurerEntry ? auth()->id() : null,
        ];

        if ($request->hasFile('proof_path')) {
            $data['proof_path'] = $request->file('proof_path')->store('proofs/rotating', 'public');
        }

        RotatingContribution::create($data);

        return redirect()->route('admin.rotating-contributions.index')
            ->with('success', 'Cotisation de la caisse rotative enregistrée avec succès.');
    }

    /**
     * Affiche le formulaire de modification d'une cotisation existante.
     */
    public function edit(RotatingContribution $rotatingContribution)
    {
        $this->authorize('manage', $rotatingContribution);

        $tontines = Tontine::all();
        $users = User::all();

        return view('admin.rotating_contributions.edit', compact('rotatingContribution', 'tontines', 'users'));
    }

    /**
     * Met à jour une cotisation existante.
     */
    public function update(Request $request, RotatingContribution $rotatingContribution)
    {
        $this->authorize('manage', $rotatingContribution);

        $validated = $request->validate([
            'tontine_id' => 'required|exists:tontines,id',
            'user_id'    => 'required|exists:users,id',
            'week'       => 'required|date',
            'amount'     => 'required|integer|min:0',
            'proof_path' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('proof_path')) {
            if ($rotatingContribution->proof_path) {
                Storage::disk('public')->delete($rotatingContribution->proof_path);
            }
            $validated['proof_path'] = $request->file('proof_path')->store('proofs/rotating', 'public');
        }

        $rotatingContribution->update($validated);

        return redirect()->route('admin.rotating-contributions.index')
            ->with('success', 'Cotisation mise à jour avec succès.');
    }

    /**
     * Valide une cotisation.
     */
    public function approve(Request $request, RotatingContribution $rotatingContribution)
    {
        $this->authorize('manage', $rotatingContribution);

        $rotatingContribution->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'La cotisation a été approuvée avec succès.');
    }

    /**
     * Rejette une cotisation.
     */
    public function reject(Request $request, RotatingContribution $rotatingContribution)
    {
        $this->authorize('manage', $rotatingContribution);

        $rotatingContribution->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'La cotisation a été rejetée.');
    }

    /**
     * Supprime une cotisation de la caisse rotative.
     */
    public function destroy(RotatingContribution $rotatingContribution)
    {
        $this->authorize('manage', $rotatingContribution);

        if ($rotatingContribution->proof_path) {
            Storage::disk('public')->delete($rotatingContribution->proof_path);
        }

        $rotatingContribution->delete();

        return redirect()->route('admin.rotating-contributions.index')
            ->with('success', 'Cotisation supprimée avec succès.');
    }
}
