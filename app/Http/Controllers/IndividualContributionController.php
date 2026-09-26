<?php

namespace App\Http\Controllers;

use App\Models\IndividualContribution;
use App\Models\Tontine;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class IndividualContributionController extends Controller
{
    /**
     * Affiche la caisse individuelle.
     *
     * Le membre ne voit que ses propres cotisations.
     * Le trésorier voit les cotisations de tous les membres.
     */
    public function index(Request $request)
    {
        $activeTontine = Tontine::where(
            'status',
            'active'
        )->first();

        $isTreasurer =
            auth()->user()->role === 'treasurer';

        /*
         * Les utilisateurs ne sont récupérés que pour
         * le trésorier, car lui seul peut choisir un membre.
         */
        $users = $isTreasurer
            ? User::all()
            : collect();

        $contributions = collect();

        if ($activeTontine) {

            $query = IndividualContribution::with([
                'user',
                'approvedBy',
            ])
                ->where(
                    'tontine_id',
                    $activeTontine->id
                );

            /*
             * Membre normal :
             * uniquement ses propres cotisations.
             */
            if (!$isTreasurer) {

                $query->where(
                    'user_id',
                    auth()->id()
                );
            }

            /*
             * Filtres disponibles uniquement pour le trésorier.
             */
            if ($isTreasurer) {

                if ($request->filled('user_id')) {

                    $query->where(
                        'user_id',
                        $request->user_id
                    );
                }

                if ($request->filled('status')) {

                    $query->where(
                        'status',
                        $request->status
                    );
                }
            }

            /*
             * Les dépôts les plus récents apparaissent en premier.
             */
            $contributions =
                $query
                    ->latest('month')
                    ->latest('id')
                    ->paginate(15)
                    ->withQueryString();
        }

        /*
         * Total personnel réellement payé.
         */
        $myTotalPaid = 0;

        if ($activeTontine) {

            $myTotalPaid =
                IndividualContribution::where(
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
        }

        return view(
            'admin.individual_contributions.index',
            compact(
                'contributions',
                'activeTontine',
                'isTreasurer',
                'users',
                'myTotalPaid'
            )
        );
    }


    /**
     * Enregistre un dépôt individuel.
     *
     * La date est libre :
     * aucun mois ou aucune semaine n'est imposé.
     */
    public function store(Request $request)
    {
        $activeTontine = Tontine::where(
            'status',
            'active'
        )->first();

        if (!$activeTontine) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Aucune tontine active pour le moment.'
                );
        }

        $isTreasurer =
            auth()->user()->role === 'treasurer';

        $rules = [

            /*
             * Date exacte du dépôt.
             */
            'month' => [
                'required',
                'date',
            ],

            /*
             * Montant libre.
             */
            'amount' => [
                'required',
                'integer',
                'min:1',
            ],

            'proof_path' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048',
            ],
        ];

        /*
         * Seul le trésorier peut enregistrer
         * une cotisation pour quelqu'un d'autre.
         */
        if ($isTreasurer) {

            $rules['user_id'] = [
                'required',
                'exists:users,id',
            ];
        }

        $validated =
            $request->validate($rules);

        $userId =
            $isTreasurer &&
            $request->filled('user_id')
                ? $request->input('user_id')
                : auth()->id();

        /*
         * La date saisie est normalisée.
         * On conserve le jour exact.
         */
        $paymentDate = Carbon::parse(
            $validated['month']
        );

        /*
         * Vérifie que le dépôt appartient à la
         * période de la tontine active.
         */
        if (
            $paymentDate->lt(
                $activeTontine->start_date
            ) ||
            $paymentDate->gt(
                $activeTontine->end_date
            )
        ) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'La date du dépôt doit être comprise '
                    . 'dans la période de la tontine.'
                );
        }

        /*
         * Le trésorier qui enregistre son propre
         * dépôt le valide automatiquement.
         */
        $isSelfTreasurerEntry =
            $isTreasurer &&
            (int) $userId ===
            (int) auth()->id();

        $data = [

            'tontine_id' =>
                $activeTontine->id,

            'user_id' =>
                $userId,

            /*
             * On conserve la colonne "month"
             * mais elle contient désormais la date
             * exacte du paiement.
             */
            'month' =>
                $paymentDate,

            'amount' =>
                $validated['amount'],

            'status' =>
                $isSelfTreasurerEntry
                    ? 'approved'
                    : 'pending',

            'approved_by' =>
                $isSelfTreasurerEntry
                    ? auth()->id()
                    : null,
        ];

        if ($request->hasFile('proof_path')) {

            $data['proof_path'] =
                $request
                    ->file('proof_path')
                    ->store(
                        'proofs/individual',
                        'public'
                    );
        }

        IndividualContribution::create($data);

        return redirect()
            ->route(
                'admin.individual-contributions.index'
            )
            ->with(
                'success',
                'Dépôt individuel enregistré avec succès.'
            );
    }


    /**
     * Formulaire de modification.
     */
    public function edit(
        IndividualContribution $individualContribution
    ) {
        $this->authorize(
            'manage',
            $individualContribution
        );

        $tontines = Tontine::all();
        $users = User::all();

        return view(
            'admin.individual_contributions.edit',
            compact(
                'individualContribution',
                'tontines',
                'users'
            )
        );
    }


    /**
     * Met à jour un dépôt.
     */
    public function update(
        Request $request,
        IndividualContribution $individualContribution
    ) {
        $this->authorize(
            'manage',
            $individualContribution
        );

        $validated = $request->validate([

            'tontine_id' => [
                'required',
                'exists:tontines,id',
            ],

            'user_id' => [
                'required',
                'exists:users,id',
            ],

            /*
             * Date exacte du paiement.
             */
            'month' => [
                'required',
                'date',
            ],

            'amount' => [
                'required',
                'integer',
                'min:1',
            ],

            'proof_path' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048',
            ],
        ]);

        if ($request->hasFile('proof_path')) {

            if (
                $individualContribution->proof_path
            ) {

                Storage::disk('public')
                    ->delete(
                        $individualContribution
                            ->proof_path
                    );
            }

            $validated['proof_path'] =
                $request
                    ->file('proof_path')
                    ->store(
                        'proofs/individual',
                        'public'
                    );
        }

        $individualContribution->update(
            $validated
        );

        return redirect()
            ->route(
                'admin.individual-contributions.index'
            )
            ->with(
                'success',
                'Dépôt individuel mis à jour avec succès.'
            );
    }


    /**
     * Approuve un dépôt.
     */
    public function approve(
        Request $request,
        IndividualContribution $individualContribution
    ) {
        $this->authorize(
            'manage',
            $individualContribution
        );

        $individualContribution->update([

            'status' =>
                'approved',

            'approved_by' =>
                auth()->id(),
        ]);

        return back()->with(
            'success',
            'Le dépôt individuel a été approuvé avec succès.'
        );
    }


    /**
     * Rejette un dépôt.
     */
    public function reject(
        Request $request,
        IndividualContribution $individualContribution
    ) {
        $this->authorize(
            'manage',
            $individualContribution
        );

        $individualContribution->update([

            'status' =>
                'rejected',

            'approved_by' =>
                auth()->id(),
        ]);

        return back()->with(
            'success',
            'Le dépôt individuel a été rejeté.'
        );
    }


    /**
     * Supprime un dépôt.
     */
    public function destroy(
        IndividualContribution $individualContribution
    ) {
        $this->authorize(
            'manage',
            $individualContribution
        );

        if (
            $individualContribution->proof_path
        ) {

            Storage::disk('public')
                ->delete(
                    $individualContribution
                        ->proof_path
                );
        }

        $individualContribution->delete();

        return redirect()
            ->route(
                'admin.individual-contributions.index'
            )
            ->with(
                'success',
                'Dépôt individuel supprimé avec succès.'
            );
    }
}