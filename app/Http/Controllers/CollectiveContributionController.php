<?php

namespace App\Http\Controllers;

use App\Models\CollectiveContribution;
use App\Models\Tontine;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;

class CollectiveContributionController extends Controller
{
    /**
     * Affiche la caisse collective regroupée par mois.
     */
    public function index(Request $request)
    {
        $tontines = Tontine::all();

        $activeTontine = Tontine::where(
            'status',
            'active'
        )->first();

        $isTreasurer =
            auth()->user()->role === 'treasurer';

        $users = User::all();

        $monthlyContributions = collect();

        if ($activeTontine) {

            /*
             * Tous les utilisateurs sont considérés
             * comme membres de la tontine.
             */
            $members = $users;

            /*
             * Récupération des cotisations de la tontine active.
             */
            $contributions = $activeTontine
                ->collectiveContributions()
                ->with([
                    'user',
                    'approvedBy',
                ])
                ->get()
                ->groupBy(function ($contribution) {

                    return $contribution->month
                        ->format('Y-m-d');
                });

            /*
             * Génération de tous les mois couverts
             * par la tontine.
             */
            $period = CarbonPeriod::create(
                $activeTontine
                    ->start_date
                    ->copy()
                    ->startOfMonth(),

                '1 month',

                $activeTontine
                    ->end_date
                    ->copy()
                    ->startOfMonth()
            );

            foreach ($period as $date) {

                $month = $date->format('Y-m-d');

                $monthContributions =
                    $contributions->get(
                        $month,
                        collect()
                    );

                /*
                 * Une seule cotisation possible
                 * par membre et par mois.
                 */
                $contributionsByUser =
                    $monthContributions->keyBy(
                        'user_id'
                    );

                /*
                 * Création du statut de chaque membre
                 * pour ce mois.
                 */
                $membersStatus = $members->map(
                    function ($user) use (
                        $contributionsByUser
                    ) {

                        $contribution =
                            $contributionsByUser->get(
                                $user->id
                            );

                        if (!$contribution) {

                            $status = 'unpaid';

                        } elseif (
                            $contribution->status
                            === 'approved'
                        ) {

                            $status = 'paid';

                        } elseif (
                            $contribution->status
                            === 'pending'
                        ) {

                            $status = 'pending';

                        } else {

                            $status = 'rejected';
                        }

                        return [
                            'user' =>
                                $user,

                            'contribution' =>
                                $contribution,

                            'status' =>
                                $status,
                        ];
                    }
                );

                /*
                 * Statistiques du mois.
                 */
                $paidCount =
                    $membersStatus
                        ->where(
                            'status',
                            'paid'
                        )
                        ->count();

                $pendingCount =
                    $membersStatus
                        ->where(
                            'status',
                            'pending'
                        )
                        ->count();

                $rejectedCount =
                    $membersStatus
                        ->where(
                            'status',
                            'rejected'
                        )
                        ->count();

                $unpaidCount =
                    $membersStatus
                        ->where(
                            'status',
                            'unpaid'
                        )
                        ->count();

                $totalPaid =
                    $membersStatus
                        ->where(
                            'status',
                            'paid'
                        )
                        ->sum(function ($item) {

                            return $item['contribution']
                                ->amount ?? 0;
                        });

                $totalPending =
                    $membersStatus
                        ->where(
                            'status',
                            'pending'
                        )
                        ->sum(function ($item) {

                            return $item['contribution']
                                ->amount ?? 0;
                        });

                /*
                 * Montant total attendu pour le mois.
                 */
                $totalExpected =
                    $members->count()
                    * $activeTontine->collective_amount;

                $monthlyContributions->push([
                    'month' =>
                        $month,

                    'label' =>
                        ucfirst(
                            $date->translatedFormat(
                                'F Y'
                            )
                        ),

                    'members' =>
                        $membersStatus,

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
        $myMonthsElapsed = 0;

        if ($activeTontine) {

            /*
             * Total réellement payé.
             */
            $myTotalPaid =
                CollectiveContribution::where(
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

            /*
             * Nombre de mois écoulés.
             */
            $today = now();

            $end =
                $activeTontine
                    ->end_date
                    ->min($today);

            $myMonthsElapsed =
                max(
                    1,
                    $activeTontine
                        ->start_date
                        ->diffInMonths($end) + 1
                );

            /*
             * Montant attendu.
             */
            $myExpectedTotal =
                $myMonthsElapsed
                * $activeTontine->collective_amount;
        }

        return view(
            'admin.collective_contributions.index',
            compact(
                'tontines',
                'activeTontine',
                'isTreasurer',
                'users',
                'monthlyContributions',
                'myTotalPaid',
                'myExpectedTotal',
                'myMonthsElapsed'
            )
        );
    }

    /**
     * Enregistre une nouvelle cotisation.
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
            'month' => [
                'required',
                'date_format:Y-m-d',
            ],

            'proof_path' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048',
            ],
        ];

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
         * Vérification que le mois appartient
         * bien à la période de la tontine.
         */
        $month = Carbon::createFromFormat(
            'Y-m-d',
            $validated['month']
        )->startOfMonth();

        $startMonth =
            $activeTontine
                ->start_date
                ->copy()
                ->startOfMonth();

        $endMonth =
            $activeTontine
                ->end_date
                ->copy()
                ->startOfMonth();

        if (
            $month->lt($startMonth) ||
            $month->gt($endMonth)
        ) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Le mois sélectionné ne fait pas partie '
                    . 'de la période de la tontine.'
                );
        }

        /*
         * Vérification d'une cotisation existante.
         */
        $existingContribution =
            CollectiveContribution::where(
                'tontine_id',
                $activeTontine->id
            )
                ->where(
                    'user_id',
                    $userId
                )
                ->whereDate(
                    'month',
                    $month
                )
                ->first();

        if ($existingContribution) {

            if (
                $existingContribution->status
                === 'approved'
            ) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Cette cotisation a déjà été payée.'
                    );
            }

            if (
                $existingContribution->status
                === 'pending'
            ) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Une cotisation est déjà en attente '
                        . 'de validation pour ce mois.'
                    );
            }

            /*
             * Si elle était rejetée, on permet
             * de refaire une déclaration.
             */
            if (
                $existingContribution->status
                === 'rejected'
            ) {

                $data = [
                    'amount' =>
                        $activeTontine
                            ->collective_amount,

                    'status' =>
                        (
                            $isTreasurer &&
                            (int) $userId ===
                            (int) auth()->id()
                        )
                            ? 'approved'
                            : 'pending',

                    'approved_by' =>
                        (
                            $isTreasurer &&
                            (int) $userId ===
                            (int) auth()->id()
                        )
                            ? auth()->id()
                            : null,
                ];

                if ($request->hasFile('proof_path')) {

                    if (
                        $existingContribution
                            ->proof_path
                    ) {

                        Storage::disk('public')
                            ->delete(
                                $existingContribution
                                    ->proof_path
                            );
                    }

                    $data['proof_path'] =
                        $request
                            ->file('proof_path')
                            ->store(
                                'proofs/collective',
                                'public'
                            );
                }

                $existingContribution->update($data);

                return redirect()
                    ->route(
                        'admin.collective-contributions.index'
                    )
                    ->with(
                        'success',
                        'La cotisation a été enregistrée à nouveau.'
                    );
            }
        }

        /*
         * Le trésorier qui enregistre sa propre
         * cotisation valide automatiquement.
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

            'month' =>
                $month,

            'amount' =>
                $activeTontine->collective_amount,

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
                        'proofs/collective',
                        'public'
                    );
        }

        CollectiveContribution::create($data);

        return redirect()
            ->route(
                'admin.collective-contributions.index'
            )
            ->with(
                'success',
                'Cotisation de la caisse collective '
                . 'enregistrée avec succès.'
            );
    }

    /**
     * Formulaire de modification.
     */
    public function edit(
        CollectiveContribution $collectiveContribution
    ) {
        $this->authorize(
            'manage',
            $collectiveContribution
        );

        $tontines = Tontine::all();
        $users = User::all();

        return view(
            'admin.collective_contributions.edit',
            compact(
                'collectiveContribution',
                'tontines',
                'users'
            )
        );
    }

    /**
     * Mise à jour.
     */
    public function update(
        Request $request,
        CollectiveContribution $collectiveContribution
    ) {
        $this->authorize(
            'manage',
            $collectiveContribution
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

            'month' => [
                'required',
                'date',
            ],

            'amount' => [
                'required',
                'integer',
                'min:0',
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
                $collectiveContribution
                    ->proof_path
            ) {

                Storage::disk('public')->delete(
                    $collectiveContribution
                        ->proof_path
                );
            }

            $validated['proof_path'] =
                $request
                    ->file('proof_path')
                    ->store(
                        'proofs/collective',
                        'public'
                    );
        }

        $collectiveContribution->update(
            $validated
        );

        return redirect()
            ->route(
                'admin.collective-contributions.index'
            )
            ->with(
                'success',
                'Cotisation mise à jour avec succès.'
            );
    }

    /**
     * Approuve une cotisation.
     */
    public function approve(
        Request $request,
        CollectiveContribution $collectiveContribution
    ) {
        $this->authorize(
            'manage',
            $collectiveContribution
        );

        $collectiveContribution->update([
            'status' =>
                'approved',

            'approved_by' =>
                auth()->id(),
        ]);

        return back()->with(
            'success',
            'La cotisation a été approuvée avec succès.'
        );
    }

    /**
     * Rejette une cotisation.
     */
    public function reject(
        Request $request,
        CollectiveContribution $collectiveContribution
    ) {
        $this->authorize(
            'manage',
            $collectiveContribution
        );

        $collectiveContribution->update([
            'status' =>
                'rejected',

            'approved_by' =>
                auth()->id(),
        ]);

        return back()->with(
            'success',
            'La cotisation a été rejetée.'
        );
    }

    /**
     * Supprime une cotisation.
     */
    public function destroy(
        CollectiveContribution $collectiveContribution
    ) {
        $this->authorize(
            'manage',
            $collectiveContribution
        );

        if (
            $collectiveContribution->proof_path
        ) {

            Storage::disk('public')->delete(
                $collectiveContribution
                    ->proof_path
            );
        }

        $collectiveContribution->delete();

        return redirect()
            ->route(
                'admin.collective-contributions.index'
            )
            ->with(
                'success',
                'Cotisation supprimée avec succès.'
            );
    }
}