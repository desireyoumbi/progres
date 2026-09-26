@extends('layouts.app')

@section('title', 'Bénéficiaires')

@section('content')

<div class="space-y-6">

{{-- =========================================================
     EN-TÊTE
========================================================== --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

    <div>
        <div class="flex items-center gap-3">

            <div class="w-11 h-11 rounded-xl bg-blue-100 dark:bg-blue-900/40
                        flex items-center justify-center">

                <i class="fa-solid fa-users text-blue-600 dark:text-blue-400 text-lg"></i>

            </div>

            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Bénéficiaires
                </h1>

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Gestion de la caisse rotative mensuelle
                </p>
            </div>

        </div>
    </div>

    @if($activeTontine && $isTreasurer)

        <button
            type="button"
            onclick="openBeneficiaryModal()"
            class="inline-flex items-center justify-center gap-2
                   px-4 py-2.5 rounded-xl
                   bg-blue-600 hover:bg-blue-700
                   text-white font-medium
                   shadow-sm transition"
        >
            <i class="fa-solid fa-plus"></i>
            Désigner un bénéficiaire
        </button>

    @endif

</div>


{{-- =========================================================
     MESSAGES
========================================================== --}}

@if(session('success'))

    <div class="flex items-start gap-3 p-4 rounded-xl
                bg-green-50 dark:bg-green-900/20
                border border-green-200 dark:border-green-800
                text-green-800 dark:text-green-300">

        <i class="fa-solid fa-circle-check mt-0.5"></i>

        <div class="flex-1 text-sm">
            {{ session('success') }}
        </div>

        <button
            type="button"
            onclick="this.parentElement.remove()"
            class="text-green-600 dark:text-green-400 hover:opacity-70"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>

    </div>

@endif


@if(session('error'))

    <div class="flex items-start gap-3 p-4 rounded-xl
                bg-red-50 dark:bg-red-900/20
                border border-red-200 dark:border-red-800
                text-red-800 dark:text-red-300">

        <i class="fa-solid fa-circle-exclamation mt-0.5"></i>

        <div class="flex-1 text-sm">
            {{ session('error') }}
        </div>

        <button
            type="button"
            onclick="this.parentElement.remove()"
            class="text-red-600 dark:text-red-400 hover:opacity-70"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>

    </div>

@endif


{{-- =========================================================
     ERREURS DE VALIDATION
========================================================== --}}

@if($errors->any())

    <div class="p-4 rounded-xl
                bg-red-50 dark:bg-red-900/20
                border border-red-200 dark:border-red-800
                text-red-800 dark:text-red-300">

        <div class="flex items-center gap-2 font-semibold mb-2">

            <i class="fa-solid fa-circle-exclamation"></i>

            Vérifie les informations saisies.

        </div>

        <ul class="list-disc list-inside text-sm space-y-1">

            @foreach($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

@endif


{{-- =========================================================
     AUCUNE TONTINE ACTIVE
========================================================== --}}

@if(!$activeTontine)

    <div class="bg-white dark:bg-gray-800
                border border-gray-200 dark:border-gray-700
                rounded-2xl shadow-sm">

        <div class="p-10 text-center">

            <div class="w-16 h-16 mx-auto mb-4 rounded-full
                        bg-gray-100 dark:bg-gray-700
                        flex items-center justify-center">

                <i class="fa-solid fa-circle-info
                          text-gray-400 text-2xl"></i>

            </div>

            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Aucune tontine active
            </h2>

            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Impossible de gérer les bénéficiaires tant qu'aucune
                tontine n'est active.
            </p>

        </div>

    </div>

@else

    {{-- =========================================================
         INFORMATIONS TONTINE
    ========================================================== --}}

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- TONTINE --}}
        <div class="bg-white dark:bg-gray-800
                    border border-gray-200 dark:border-gray-700
                    rounded-2xl shadow-sm">

            <div class="p-5 flex items-center gap-4">

                <div class="w-12 h-12 rounded-xl
                            bg-blue-100 dark:bg-blue-900/40
                            flex items-center justify-center">

                    <i class="fa-solid fa-users
                              text-blue-600 dark:text-blue-400"></i>

                </div>

                <div class="min-w-0">

                    <p class="text-xs font-medium uppercase tracking-wide
                              text-gray-500 dark:text-gray-400">
                        Tontine active
                    </p>

                    <p class="mt-1 font-semibold text-gray-900 dark:text-white truncate">
                        {{ $activeTontine->name }}
                    </p>

                </div>

            </div>

        </div>


        {{-- MONTANT MENSUEL --}}
        <div class="bg-white dark:bg-gray-800
                    border border-gray-200 dark:border-gray-700
                    rounded-2xl shadow-sm">

            <div class="p-5 flex items-center gap-4">

                <div class="w-12 h-12 rounded-xl
                            bg-green-100 dark:bg-green-900/40
                            flex items-center justify-center">

                    <i class="fa-solid fa-money-bill-wave
                              text-green-600 dark:text-green-400"></i>

                </div>

                <div>

                    <p class="text-xs font-medium uppercase tracking-wide
                              text-gray-500 dark:text-gray-400">
                        Montant mensuel
                    </p>

                    <p class="mt-1 font-semibold text-gray-900 dark:text-white">

                        {{ number_format(
                            $monthlyAmount,
                            0,
                            ',',
                            ' '
                        ) }}

                        FCFA

                    </p>

                </div>

            </div>

        </div>


        {{-- COTISATION --}}
        <div class="bg-white dark:bg-gray-800
                    border border-gray-200 dark:border-gray-700
                    rounded-2xl shadow-sm">

            <div class="p-5 flex items-center gap-4">

                <div class="w-12 h-12 rounded-xl
                            bg-amber-100 dark:bg-amber-900/40
                            flex items-center justify-center">

                    <i class="fa-solid fa-calendar-days
                              text-amber-600 dark:text-amber-400"></i>

                </div>

                <div>

                    <p class="text-xs font-medium uppercase tracking-wide
                              text-gray-500 dark:text-gray-400">
                        Cotisation hebdomadaire
                    </p>

                    <p class="mt-1 font-semibold text-gray-900 dark:text-white">

                        {{ number_format(
                            $activeTontine->rotating_amount,
                            0,
                            ',',
                            ' '
                        ) }}

                        FCFA

                    </p>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         ROTATION DES BÉNÉFICIAIRES
    ========================================================== --}}

    <div class="bg-white dark:bg-gray-800
                border border-gray-200 dark:border-gray-700
                rounded-2xl shadow-sm overflow-hidden">

        {{-- HEADER --}}
        <div class="px-5 py-4
                    border-b border-gray-200 dark:border-gray-700
                    flex flex-col sm:flex-row
                    sm:items-center sm:justify-between gap-3">

            <div>

                <h2 class="font-semibold text-gray-900 dark:text-white
                           flex items-center gap-2">

                    <i class="fa-solid fa-hand-holding-dollar
                              text-blue-600 dark:text-blue-400"></i>

                    Rotation des bénéficiaires

                </h2>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Une personne reçoit la caisse à la fin de chaque mois.
                </p>

            </div>

            <span class="inline-flex items-center self-start
                         px-3 py-1 rounded-full
                         text-xs font-semibold
                         bg-blue-100 dark:bg-blue-900/40
                         text-blue-700 dark:text-blue-300">

                {{ $beneficiaries->count() }}
                mois enregistré(s)

            </span>

        </div>


        {{-- CONTENU --}}
        @if($beneficiaries->isEmpty())

            <div class="p-10 text-center">

                <div class="w-16 h-16 mx-auto mb-4 rounded-full
                            bg-gray-100 dark:bg-gray-700
                            flex items-center justify-center">

                    <i class="fa-solid fa-calendar-xmark
                              text-gray-400 text-2xl"></i>

                </div>

                <h3 class="font-semibold text-gray-900 dark:text-white">
                    Aucun bénéficiaire désigné
                </h3>

                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Commence par désigner le bénéficiaire du premier mois.
                </p>

                @if($isTreasurer)

                    <button
                        type="button"
                        onclick="openBeneficiaryModal()"
                        class="mt-5 inline-flex items-center gap-2
                               px-4 py-2.5 rounded-xl
                               bg-blue-600 hover:bg-blue-700
                               text-white font-medium transition"
                    >
                        <i class="fa-solid fa-plus"></i>
                        Désigner un bénéficiaire
                    </button>

                @endif

            </div>

        @else

            {{-- TABLEAU DESKTOP --}}
            <div class="hidden md:block overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50 dark:bg-gray-900/50
                                  text-gray-600 dark:text-gray-400">

                        <tr>

                            <th class="px-5 py-3 text-left font-semibold">
                                Mois
                            </th>

                            <th class="px-5 py-3 text-left font-semibold">
                                Bénéficiaire
                            </th>

                            <th class="px-5 py-3 text-left font-semibold">
                                Désigné par
                            </th>

                            <th class="px-5 py-3 text-left font-semibold">
                                Statut
                            </th>

                            <th class="px-5 py-3 text-left font-semibold">
                                Date de paiement
                            </th>

                            @if($isTreasurer)

                                <th class="px-5 py-3 text-right font-semibold">
                                    Actions
                                </th>

                            @endif

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">

                        @foreach($beneficiaries as $beneficiary)

                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">

                                {{-- MOIS --}}
                                <td class="px-5 py-4">

                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        {{ $beneficiary->month->translatedFormat('F Y') }}
                                    </span>

                                </td>


                                {{-- BÉNÉFICIAIRE --}}
                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-3">

                                        <div class="w-9 h-9 rounded-full
                                                    bg-blue-100 dark:bg-blue-900/40
                                                    flex items-center justify-center">

                                            <i class="fa-solid fa-user
                                                      text-blue-600 dark:text-blue-400 text-sm"></i>

                                        </div>

                                        <span class="font-medium text-gray-900 dark:text-white">
                                            {{ $beneficiary->user->name }}
                                        </span>

                                    </div>

                                </td>


                                {{-- DÉSIGNÉ PAR --}}
                                <td class="px-5 py-4 text-gray-600 dark:text-gray-300">

                                    @if($beneficiary->assignedBy)

                                        {{ $beneficiary->assignedBy->name }}

                                    @else

                                        <span class="text-gray-400">—</span>

                                    @endif

                                </td>


                                {{-- STATUT --}}
                                <td class="px-5 py-4">

                                    @if($beneficiary->status === 'pending')

                                        <span class="inline-flex items-center gap-1.5
                                                     px-2.5 py-1 rounded-full
                                                     text-xs font-semibold
                                                     bg-amber-100 dark:bg-amber-900/30
                                                     text-amber-700 dark:text-amber-300">

                                            <i class="fa-solid fa-clock"></i>
                                            En attente

                                        </span>

                                    @elseif($beneficiary->status === 'paid')

                                        <span class="inline-flex items-center gap-1.5
                                                     px-2.5 py-1 rounded-full
                                                     text-xs font-semibold
                                                     bg-green-100 dark:bg-green-900/30
                                                     text-green-700 dark:text-green-300">

                                            <i class="fa-solid fa-check"></i>
                                            Payé

                                        </span>

                                    @elseif($beneficiary->status === 'cancelled')

                                        <span class="inline-flex items-center gap-1.5
                                                     px-2.5 py-1 rounded-full
                                                     text-xs font-semibold
                                                     bg-red-100 dark:bg-red-900/30
                                                     text-red-700 dark:text-red-300">

                                            <i class="fa-solid fa-xmark"></i>
                                            Annulé

                                        </span>

                                    @endif

                                </td>


                                {{-- DATE PAIEMENT --}}
                                <td class="px-5 py-4 text-gray-600 dark:text-gray-300">

                                    @if($beneficiary->paid_at)

                                        {{ $beneficiary->paid_at->format('d/m/Y à H:i') }}

                                    @else

                                        <span class="text-gray-400">—</span>

                                    @endif

                                </td>


                                {{-- ACTIONS --}}
                                @if($isTreasurer)

                                    <td class="px-5 py-4 text-right">

                                        @if($beneficiary->status === 'pending')

                                            <div class="flex justify-end gap-2">

                                                <form
                                                    action="{{ route(
                                                        'admin.beneficiaries.confirm-payment',
                                                        $beneficiary
                                                    ) }}"
                                                    method="POST"
                                                >

                                                    @csrf
                                                    @method('PATCH')

                                                    <button
                                                        type="submit"
                                                        onclick="return confirm(
                                                            'Confirmer que {{ $beneficiary->user->name }} a bien reçu la caisse ?'
                                                        )"
                                                        class="inline-flex items-center gap-1.5
                                                               px-3 py-2 rounded-lg
                                                               bg-green-600 hover:bg-green-700
                                                               text-white text-xs font-medium
                                                               transition"
                                                    >
                                                        <i class="fa-solid fa-check"></i>
                                                        Confirmer
                                                    </button>

                                                </form>


                                                <form
                                                    action="{{ route(
                                                        'admin.beneficiaries.cancel',
                                                        $beneficiary
                                                    ) }}"
                                                    method="POST"
                                                >

                                                    @csrf
                                                    @method('PATCH')

                                                    <button
                                                        type="submit"
                                                        onclick="return confirm(
                                                            'Annuler cette désignation ?'
                                                        )"
                                                        class="inline-flex items-center gap-1.5
                                                               px-3 py-2 rounded-lg
                                                               border border-red-300
                                                               dark:border-red-700
                                                               text-red-600 dark:text-red-400
                                                               hover:bg-red-50
                                                               dark:hover:bg-red-900/20
                                                               text-xs font-medium
                                                               transition"
                                                    >
                                                        <i class="fa-solid fa-xmark"></i>
                                                        Annuler
                                                    </button>

                                                </form>

                                            </div>

                                        @elseif($beneficiary->status === 'paid')

                                            <span class="text-xs font-medium text-green-600 dark:text-green-400">

                                                <i class="fa-solid fa-circle-check mr-1"></i>
                                                Paiement effectué

                                            </span>

                                        @elseif($beneficiary->status === 'cancelled')

                                            <button
                                                type="button"
                                                onclick="openBeneficiaryModal('{{ $beneficiary->month->format('Y-m') }}')"
                                                class="inline-flex items-center gap-1.5
                                                       px-3 py-2 rounded-lg
                                                       border border-blue-300
                                                       dark:border-blue-700
                                                       text-blue-600 dark:text-blue-400
                                                       hover:bg-blue-50
                                                       dark:hover:bg-blue-900/20
                                                       text-xs font-medium
                                                       transition"
                                            >
                                                <i class="fa-solid fa-rotate-right"></i>
                                                Désigner à nouveau
                                            </button>

                                        @endif

                                    </td>

                                @endif

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- CARTES MOBILE --}}
            <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700">

                @foreach($beneficiaries as $beneficiary)

                    <div class="p-4">

                        <div class="flex items-start justify-between gap-3">

                            <div>

                                <p class="font-semibold text-gray-900 dark:text-white">
                                    {{ $beneficiary->month->translatedFormat('F Y') }}
                                </p>

                                <div class="flex items-center gap-2 mt-2">

                                    <div class="w-8 h-8 rounded-full
                                                bg-blue-100 dark:bg-blue-900/40
                                                flex items-center justify-center">

                                        <i class="fa-solid fa-user
                                                  text-blue-600 dark:text-blue-400 text-xs"></i>

                                    </div>

                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $beneficiary->user->name }}
                                    </span>

                                </div>

                            </div>


                            @if($beneficiary->status === 'pending')

                                <span class="inline-flex items-center gap-1
                                             px-2 py-1 rounded-full
                                             text-xs font-semibold
                                             bg-amber-100 dark:bg-amber-900/30
                                             text-amber-700 dark:text-amber-300">

                                    <i class="fa-solid fa-clock"></i>
                                    En attente

                                </span>

                            @elseif($beneficiary->status === 'paid')

                                <span class="inline-flex items-center gap-1
                                             px-2 py-1 rounded-full
                                             text-xs font-semibold
                                             bg-green-100 dark:bg-green-900/30
                                             text-green-700 dark:text-green-300">

                                    <i class="fa-solid fa-check"></i>
                                    Payé

                                </span>

                            @else

                                <span class="inline-flex items-center gap-1
                                             px-2 py-1 rounded-full
                                             text-xs font-semibold
                                             bg-red-100 dark:bg-red-900/30
                                             text-red-700 dark:text-red-300">

                                    <i class="fa-solid fa-xmark"></i>
                                    Annulé

                                </span>

                            @endif

                        </div>


                        <div class="mt-3 text-xs text-gray-500 dark:text-gray-400 space-y-1">

                            <p>

                                <span class="font-medium">
                                    Désigné par :
                                </span>

                                @if($beneficiary->assignedBy)

                                    {{ $beneficiary->assignedBy->name }}

                                @else

                                    —

                                @endif

                            </p>

                            <p>

                                <span class="font-medium">
                                    Paiement :
                                </span>

                                @if($beneficiary->paid_at)

                                    {{ $beneficiary->paid_at->format('d/m/Y à H:i') }}

                                @else

                                    —

                                @endif

                            </p>

                        </div>


                        @if($isTreasurer)

                            <div class="mt-4 flex flex-wrap gap-2">

                                @if($beneficiary->status === 'pending')

                                    <form
                                        action="{{ route(
                                            'admin.beneficiaries.confirm-payment',
                                            $beneficiary
                                        ) }}"
                                        method="POST"
                                        class="flex-1"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            onclick="return confirm(
                                                'Confirmer que {{ $beneficiary->user->name }} a bien reçu la caisse ?'
                                            )"
                                            class="w-full inline-flex items-center justify-center gap-1.5
                                                   px-3 py-2 rounded-lg
                                                   bg-green-600 hover:bg-green-700
                                                   text-white text-xs font-medium"
                                        >
                                            <i class="fa-solid fa-check"></i>
                                            Confirmer
                                        </button>

                                    </form>


                                    <form
                                        action="{{ route(
                                            'admin.beneficiaries.cancel',
                                            $beneficiary
                                        ) }}"
                                        method="POST"
                                        class="flex-1"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            onclick="return confirm(
                                                'Annuler cette désignation ?'
                                            )"
                                            class="w-full inline-flex items-center justify-center gap-1.5
                                                   px-3 py-2 rounded-lg
                                                   border border-red-300
                                                   dark:border-red-700
                                                   text-red-600 dark:text-red-400
                                                   hover:bg-red-50
                                                   dark:hover:bg-red-900/20
                                                   text-xs font-medium"
                                        >
                                            <i class="fa-solid fa-xmark"></i>
                                            Annuler
                                        </button>

                                    </form>

                                @elseif($beneficiary->status === 'paid')

                                    <span class="text-xs text-green-600 dark:text-green-400">

                                        <i class="fa-solid fa-circle-check mr-1"></i>
                                        Paiement effectué

                                    </span>

                                @elseif($beneficiary->status === 'cancelled')

                                    <button
                                        type="button"
                                        onclick="openBeneficiaryModal('{{ $beneficiary->month->format('Y-m') }}')"
                                        class="w-full inline-flex items-center justify-center gap-2
                                               px-3 py-2 rounded-lg
                                               border border-blue-300
                                               dark:border-blue-700
                                               text-blue-600 dark:text-blue-400
                                               hover:bg-blue-50
                                               dark:hover:bg-blue-900/20
                                               text-xs font-medium"
                                    >
                                        <i class="fa-solid fa-rotate-right"></i>
                                        Désigner à nouveau
                                    </button>

                                @endif

                            </div>

                        @endif

                    </div>

                @endforeach

            </div>

        @endif

    </div>


    {{-- =========================================================
         HISTORIQUE
    ========================================================== --}}

    @php

        $paidBeneficiaries = $beneficiaries
            ->where('status', 'paid')
            ->sortByDesc('month');

    @endphp


    <div class="bg-white dark:bg-gray-800
                border border-gray-200 dark:border-gray-700
                rounded-2xl shadow-sm overflow-hidden">

        <div class="px-5 py-4
                    border-b border-gray-200 dark:border-gray-700">

            <h2 class="font-semibold text-gray-900 dark:text-white
                       flex items-center gap-2">

                <i class="fa-solid fa-clock-rotate-left
                          text-blue-600 dark:text-blue-400"></i>

                Historique des personnes ayant reçu la caisse

            </h2>

        </div>


        <div class="p-5">

            @if($paidBeneficiaries->isEmpty())

                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Aucun membre n'a encore reçu la caisse.
                </div>

            @else

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                    @foreach($paidBeneficiaries as $beneficiary)

                        <div class="p-4 rounded-xl
                                    border border-gray-200 dark:border-gray-700
                                    bg-gray-50 dark:bg-gray-900/40">

                            <div class="flex items-center gap-3">

                                <div class="w-10 h-10 rounded-full
                                            bg-green-100 dark:bg-green-900/30
                                            flex items-center justify-center">

                                    <i class="fa-solid fa-check
                                              text-green-600 dark:text-green-400"></i>

                                </div>

                                <div class="min-w-0">

                                    <p class="font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $beneficiary->user->name }}
                                    </p>

                                    <p class="text-xs text-gray-500 dark:text-gray-400">

                                        {{ $beneficiary->month->translatedFormat('F Y') }}

                                        @if($beneficiary->paid_at)

                                            · {{ $beneficiary->paid_at->format('d/m/Y') }}

                                        @endif

                                    </p>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            @endif

        </div>

    </div>

@endif

</div>

{{-- =========================================================
MODAL TAILWIND
========================================================= --}}

@if($activeTontine && $isTreasurer)


<div
    id="addBeneficiaryModal"
    class="fixed inset-0 z-[100] hidden"
    aria-hidden="true"
>

    {{-- Overlay --}}
    <div
        class="absolute inset-0 bg-black/50 backdrop-blur-sm"
        onclick="closeBeneficiaryModal()"
    ></div>


    {{-- Contenu --}}
    <div class="relative min-h-screen flex items-center justify-center p-4">

        <div
            class="relative w-full max-w-lg
                   bg-white dark:bg-gray-800
                   rounded-2xl shadow-2xl
                   border border-gray-200 dark:border-gray-700
                   overflow-hidden"
            onclick="event.stopPropagation()"
        >

            {{-- HEADER --}}
            <div class="px-6 py-4
                        border-b border-gray-200 dark:border-gray-700
                        flex items-center justify-between">

                <h2 class="text-lg font-semibold text-gray-900 dark:text-white
                           flex items-center gap-2">

                    <i class="fa-solid fa-user-plus
                              text-blue-600 dark:text-blue-400"></i>

                    Désigner un bénéficiaire

                </h2>

                <button
                    type="button"
                    onclick="closeBeneficiaryModal()"
                    class="w-9 h-9 rounded-lg
                           text-gray-500 hover:text-gray-700
                           dark:text-gray-400 dark:hover:text-gray-200
                           hover:bg-gray-100 dark:hover:bg-gray-700
                           transition"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>

            </div>


            {{-- BODY --}}
            <form
                method="POST"
                action="{{ route('admin.beneficiaries.store') }}"
            >

                @csrf

                <div class="p-6 space-y-5">

                    <div class="p-4 rounded-xl
                                bg-blue-50 dark:bg-blue-900/20
                                border border-blue-200 dark:border-blue-800
                                text-blue-800 dark:text-blue-300 text-sm">

                        <div class="flex gap-3">

                            <i class="fa-solid fa-circle-info mt-0.5"></i>

                            <p>
                                Choisis le membre qui recevra la caisse
                                pour le mois sélectionné.
                            </p>

                        </div>

                    </div>


                    {{-- MOIS --}}
                    <div>

                        <label
                            for="month"
                            class="block text-sm font-medium
                                   text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Mois concerné
                        </label>

                        <input
                            type="month"
                            name="month"
                            id="month"
                            value="{{ old('month', now()->format('Y-m')) }}"
                            required
                            class="w-full px-3 py-2.5 rounded-xl
                                   border border-gray-300 dark:border-gray-600
                                   bg-white dark:bg-gray-900
                                   text-gray-900 dark:text-white
                                   focus:ring-2 focus:ring-blue-500
                                   focus:border-blue-500 outline-none"
                        >

                        @error('month')

                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- MEMBRE --}}
                    <div>

                        <label
                            for="user_id"
                            class="block text-sm font-medium
                                   text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Bénéficiaire
                        </label>

                        <select
                            name="user_id"
                            id="user_id"
                            required
                            class="w-full px-3 py-2.5 rounded-xl
                                   border border-gray-300 dark:border-gray-600
                                   bg-white dark:bg-gray-900
                                   text-gray-900 dark:text-white
                                   focus:ring-2 focus:ring-blue-500
                                   focus:border-blue-500 outline-none"
                        >

                            <option value="">
                                -- Sélectionner un membre --
                            </option>

                            @foreach($users as $user)

                                <option
                                    value="{{ $user->id }}"
                                    @selected(old('user_id') == $user->id)
                                >
                                    {{ $user->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('user_id')

                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    <div class="p-4 rounded-xl
                                bg-amber-50 dark:bg-amber-900/20
                                border border-amber-200 dark:border-amber-800
                                text-amber-800 dark:text-amber-300 text-sm">

                        <div class="flex gap-3">

                            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>

                            <p>
                                Un membre qui a déjà reçu la caisse
                                dans le cycle actuel ne pourra pas être
                                sélectionné.
                            </p>

                        </div>

                    </div>

                </div>


                {{-- FOOTER --}}
                <div class="px-6 py-4
                            bg-gray-50 dark:bg-gray-900/40
                            border-t border-gray-200 dark:border-gray-700
                            flex justify-end gap-3">

                    <button
                        type="button"
                        onclick="closeBeneficiaryModal()"
                        class="px-4 py-2.5 rounded-xl
                               border border-gray-300 dark:border-gray-600
                               text-gray-700 dark:text-gray-300
                               hover:bg-gray-100 dark:hover:bg-gray-700
                               font-medium transition"
                    >
                        Annuler
                    </button>

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2
                               px-4 py-2.5 rounded-xl
                               bg-blue-600 hover:bg-blue-700
                               text-white font-medium transition"
                    >
                        <i class="fa-solid fa-check"></i>
                        Désigner
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


@endif

{{-- =========================================================
JAVASCRIPT DU MODAL
========================================================= --}}

@if($activeTontine && $isTreasurer)


<script>

    function openBeneficiaryModal(month = null)
    {
        const modal = document.getElementById(
            'addBeneficiaryModal'
        );

        if (!modal) {
            return;
        }

        const monthInput = document.getElementById('month');

        if (month && monthInput) {
            monthInput.value = month;
        }

        modal.classList.remove('hidden');

        document.body.classList.add('overflow-hidden');
    }


    function closeBeneficiaryModal()
    {
        const modal = document.getElementById(
            'addBeneficiaryModal'
        );

        if (!modal) {
            return;
        }

        modal.classList.add('hidden');

        document.body.classList.remove('overflow-hidden');
    }


    document.addEventListener('keydown', function(event) {

        if (event.key !== 'Escape') {
            return;
        }

        closeBeneficiaryModal();

    });


    @if($errors->any())

        document.addEventListener('DOMContentLoaded', function() {

            openBeneficiaryModal();

        });

    @endif

</script>

@endif

@endsection
