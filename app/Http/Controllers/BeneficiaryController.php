<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Tontine;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BeneficiaryController extends Controller
{
    /**
     * Vérifier que l'utilisateur connecté
     * est bien le trésorier.
     */
    private function ensureTreasurer(): void
    {
        if (auth()->user()->role !== 'treasurer') {
            abort(
                403,
                'Vous n\'êtes pas autorisé à effectuer cette action.'
            );
        }
    }


    /**
     * Afficher les bénéficiaires.
     *
     * Trésorier :
     * - voit tous les mois ;
     * - peut désigner ;
     * - peut confirmer ;
     * - peut annuler.
     *
     * Autres membres :
     * - voient uniquement le bénéficiaire du mois actuel ;
     * - voient les personnes ayant déjà bouffé ;
     * - aucune action possible.
     */
    public function index()
    {
        $activeTontine = Tontine::where(
            'status',
            'active'
        )->first();

        if (!$activeTontine) {
            return view(
                'admin.beneficiaries.index',
                [
                    'activeTontine' => null,
                    'beneficiaries' => collect(),
                    'users' => collect(),
                    'isTreasurer' => false,
                    'monthlyAmount' => 0,
                ]
            );
        }


        $isTreasurer =
            auth()->user()->role === 'treasurer';


        /*
         * Le trésorier peut voir toute l'histoire.
         */
        if ($isTreasurer) {

            $beneficiaries = Beneficiary::with([
                'user',
                'assignedBy',
            ])
                ->where(
                    'tontine_id',
                    $activeTontine->id
                )
                ->orderBy('month')
                ->get();

        } else {

            /*
             * Les autres membres ne voient que :
             *
             * 1. le bénéficiaire du mois actuel ;
             * 2. les bénéficiaires ayant déjà été payés.
             */
            $currentMonth = now()->startOfMonth();

            $beneficiaries = Beneficiary::with([
                'user',
                'assignedBy',
            ])
                ->where(
                    'tontine_id',
                    $activeTontine->id
                )
                ->where(function ($query) use ($currentMonth) {

                    $query
                        ->where('status', 'paid')
                        ->orWhere(function ($query) use ($currentMonth) {

                            $query
                                ->whereDate(
                                    'month',
                                    $currentMonth
                                )
                                ->whereIn(
                                    'status',
                                    [
                                        'pending',
                                        'cancelled',
                                    ]
                                );

                        });

                })
                ->orderBy('month')
                ->get();
        }


        /*
         * Les utilisateurs ne sont nécessaires
         * que pour le formulaire du trésorier.
         */
        $users = $isTreasurer
            ? User::all()
            : collect();


        /*
         * Calcul du montant mensuel du bénéficiaire.
         *
         * Règle métier :
         *
         * nombre de membres
         * × cotisation hebdomadaire
         * × nombre de semaines
         *
         * Un mois est plafonné à 4 semaines.
         *
         * Exemple :
         *
         * 4 membres
         * × 4 000 FCFA
         * × 4 semaines
         *
         * = 64 000 FCFA
         *
         * On ne tient pas compte du nombre réel
         * de cotisations enregistrées.
         */
        $numberOfWeeks = 4;

        $numberOfMembers = User::count();

        $monthlyAmount =
            $numberOfMembers
            * $activeTontine->rotating_amount
            * $numberOfWeeks;


        return view(
            'admin.beneficiaries.index',
            compact(
                'activeTontine',
                'beneficiaries',
                'users',
                'isTreasurer',
                'monthlyAmount'
            )
        );
    }


    /**
     * Désigner un bénéficiaire.
     *
     * SEUL LE TRÉSORIER peut effectuer cette action.
     */
    public function store(Request $request)
    {
        $this->ensureTreasurer();


        /*
         * Récupérer la tontine active.
         */
        $activeTontine = Tontine::where(
            'status',
            'active'
        )->first();

        if (!$activeTontine) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Aucune tontine active.'
                );
        }


        /*
         * Validation.
         *
         * Le champ <input type="month">
         * envoie YYYY-MM.
         */
        $validated = $request->validate([
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],

            'month' => [
                'required',
                'date_format:Y-m',
            ],
        ]);


        /*
         * Transformer YYYY-MM
         * en premier jour du mois.
         */
        $month = Carbon::createFromFormat(
            'Y-m',
            $validated['month']
        )->startOfMonth();


        /*
         * Vérifier le membre.
         */
        $user = User::find(
            $validated['user_id']
        );

        if (!$user) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Membre introuvable.'
                );
        }


        /*
         * Chercher une désignation existante
         * pour ce mois.
         */
        $existingBeneficiary = Beneficiary::where(
            'tontine_id',
            $activeTontine->id
        )
            ->whereDate(
                'month',
                $month
            )
            ->first();


        /*
         * Si le bénéficiaire a déjà été payé,
         * le mois est terminé.
         */
        if (
            $existingBeneficiary &&
            $existingBeneficiary->status === 'paid'
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Le bénéficiaire de ce mois a déjà été payé.'
                );
        }


        /*
         * Une désignation pending existe déjà.
         */
        if (
            $existingBeneficiary &&
            $existingBeneficiary->status === 'pending'
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Un bénéficiaire est déjà désigné pour ce mois.'
                );
        }


        /*
         * Récupérer uniquement les personnes
         * qui ont effectivement reçu la caisse.
         *
         * Seul le statut "paid" compte.
         */
        $paidBeneficiaries = Beneficiary::where(
            'tontine_id',
            $activeTontine->id
        )
            ->where(
                'status',
                'paid'
            )
            ->orderBy('month')
            ->orderBy('id')
            ->get();


        /*
         * Tous les utilisateurs sont actuellement
         * considérés comme membres.
         */
        $totalMembers = User::count();

        if ($totalMembers === 0) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Aucun membre n\'est disponible.'
                );
        }


        /*
         * Construire le cycle actuel.
         */
        $currentCycleUserIds = [];

        foreach ($paidBeneficiaries as $beneficiary) {

            if (
                in_array(
                    $beneficiary->user_id,
                    $currentCycleUserIds,
                    true
                )
            ) {
                continue;
            }

            $currentCycleUserIds[] =
                $beneficiary->user_id;


            /*
             * Tous les membres ont bouffé.
             * Nouveau cycle.
             */
            if (
                count($currentCycleUserIds)
                === $totalMembers
            ) {
                $currentCycleUserIds = [];
            }
        }


        /*
         * Le membre choisi a-t-il déjà bouffé
         * dans le cycle actuel ?
         */
        if (
            in_array(
                $user->id,
                $currentCycleUserIds,
                true
            )
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ce membre a déjà bénéficié de la caisse '
                    . 'dans le cycle actuel. '
                    . 'Il doit attendre que tous les autres '
                    . 'membres aient bénéficié.'
                );
        }


        /*
         * Si la désignation précédente était annulée,
         * on réutilise la même ligne.
         */
        if (
            $existingBeneficiary &&
            $existingBeneficiary->status === 'cancelled'
        ) {

            $existingBeneficiary->update([
                'user_id' => $user->id,
                'assigned_by' => auth()->id(),
                'status' => 'pending',
                'paid_at' => null,
            ]);

        } else {

            Beneficiary::create([
                'tontine_id' => $activeTontine->id,
                'month' => $month,
                'user_id' => $user->id,
                'assigned_by' => auth()->id,
                'status' => 'pending',
                'paid_at' => null,
            ]);
        }


        return redirect()
            ->route('admin.beneficiaries.index')
            ->with(
                'success',
                'Le bénéficiaire a été désigné avec succès.'
            );
    }


    /**
     * Confirmer le paiement.
     *
     * SEUL LE TRÉSORIER peut effectuer cette action.
     */
    public function confirmPayment(
        Beneficiary $beneficiary
    ) {
        $this->ensureTreasurer();


        $activeTontine = Tontine::where(
            'status',
            'active'
        )->first();


        if (
            !$activeTontine ||
            $beneficiary->tontine_id
                !== $activeTontine->id
        ) {
            abort(
                404,
                'Bénéficiaire introuvable.'
            );
        }


        if ($beneficiary->status === 'paid') {
            return back()->with(
                'error',
                'Ce bénéficiaire a déjà été payé.'
            );
        }


        if ($beneficiary->status !== 'pending') {
            return back()->with(
                'error',
                'Cette désignation ne peut pas être confirmée.'
            );
        }


        $beneficiary->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);


        return back()->with(
            'success',
            'Le paiement du bénéficiaire a été confirmé.'
        );
    }


    /**
     * Annuler une désignation.
     *
     * SEUL LE TRÉSORIER peut effectuer cette action.
     */
    public function cancel(
        Beneficiary $beneficiary
    ) {
        $this->ensureTreasurer();


        $activeTontine = Tontine::where(
            'status',
            'active'
        )->first();


        if (
            !$activeTontine ||
            $beneficiary->tontine_id
                !== $activeTontine->id
        ) {
            abort(
                404,
                'Bénéficiaire introuvable.'
            );
        }


        if ($beneficiary->status === 'paid') {
            return back()->with(
                'error',
                'Un bénéficiaire déjà payé ne peut pas être annulé.'
            );
        }


        $beneficiary->update([
            'status' => 'cancelled',
            'paid_at' => null,
        ]);


        return back()->with(
            'success',
            'La désignation a été annulée. '
            . 'Un autre bénéficiaire peut maintenant être désigné.'
        );
    }
}
