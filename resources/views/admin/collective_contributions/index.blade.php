@extends('layouts.app')

@section('title', 'Caisse Collective - PROGRÈS')

@section('content')

@php

/*
 * Mois actuel.
 */
$currentMonth = now()
    ->startOfMonth()
    ->format('Y-m-d');


/*
 * Recherche du mois actuel.
 */
$currentMonthData =
    $monthlyContributions->firstWhere(
        'month',
        $currentMonth
    );


/*
 * Mois précédents.
 * Le plus récent apparaît en premier.
 */
$pastMonths =
    $monthlyContributions
        ->filter(function ($month) use ($currentMonth) {

            return $month['month'] < $currentMonth;
        })
        ->sortByDesc('month');


/*
 * Mois futurs.
 * Le plus proche apparaît en premier.
 */
$futureMonths =
    $monthlyContributions
        ->filter(function ($month) use ($currentMonth) {

            return $month['month'] > $currentMonth;
        })
        ->sortBy('month');


/*
 * Pourcentage personnel.
 */
$percentage =
    $myExpectedTotal > 0
        ? min(
            100,
            round(
                ($myTotalPaid / $myExpectedTotal) * 100
            )
        )
        : 0;


$isUpToDate =
    $myTotalPaid >= $myExpectedTotal;

@endphp


<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- EN-TÊTE --}}

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        <div>

            <h1 class="text-2xl font-bold text-gray-800">
                Caisse collective
            </h1>

            @if($activeTontine)

                <p class="text-sm text-gray-500 mt-1">

                    {{ $activeTontine->name }}

                    —

                    Cotisation mensuelle :

                    <strong>
                        {{ number_format(
                            $activeTontine->collective_amount,
                            0,
                            ',',
                            ' '
                        ) }}
                        FCFA
                    </strong>

                </p>

            @else

                <p class="text-sm text-red-500 mt-1">
                    Aucune tontine active.
                </p>

            @endif

        </div>


        @if($activeTontine)

            <div class="flex flex-wrap gap-2">

                @if($currentMonthData)

                    <button
                        type="button"
                        onclick="scrollToCurrentMonth()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                    >

                        <i class="fa-solid fa-calendar-day"></i>

                        Mois actuel

                    </button>

                @endif


                <button
                    type="button"
                    onclick="openModal()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
                >

                    <i class="fa-solid fa-plus"></i>

                    Enregistrer une cotisation

                </button>

            </div>

        @endif

    </div>


    @if($activeTontine)

        {{-- RÉSUMÉ PERSONNEL --}}

        <div class="bg-white rounded-xl shadow-sm border p-5 mb-8">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">

                <div>

                    <p class="text-sm text-gray-500">
                        Ma caisse collective
                    </p>

                    <p class="text-2xl font-bold text-gray-800 mt-1">

                        {{ number_format(
                            $myTotalPaid,
                            0,
                            ',',
                            ' '
                        ) }}

                        <span class="text-sm font-normal text-gray-400">

                            /

                            {{ number_format(
                                $myExpectedTotal,
                                0,
                                ',',
                                ' '
                            ) }}

                            FCFA

                        </span>

                    </p>


                    <p class="text-xs mt-1
                        {{ $isUpToDate
                            ? 'text-green-600'
                            : 'text-yellow-600'
                        }}"
                    >

                        <i class="fa-solid
                            {{ $isUpToDate
                                ? 'fa-circle-check'
                                : 'fa-triangle-exclamation'
                            }}
                        mr-1"></i>


                        @if($isUpToDate)

                            À jour sur ce cycle

                        @else

                            En retard de

                            {{ number_format(
                                $myExpectedTotal - $myTotalPaid,
                                0,
                                ',',
                                ' '
                            ) }}

                            FCFA

                        @endif

                    </p>

                </div>


                <div class="w-full sm:w-48">

                    <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">

                        <div
                            class="h-3 rounded-full
                                {{ $isUpToDate
                                    ? 'bg-green-500'
                                    : 'bg-yellow-500'
                                }}"
                            style="width: {{ $percentage }}%"
                        ></div>

                    </div>

                    <p class="text-xs text-gray-400 mt-1 text-right">
                        {{ $percentage }}%
                    </p>

                </div>

            </div>

        </div>


        {{-- MESSAGES --}}

        @if(session('success'))

            <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl p-4 mb-6">

                <i class="fa-solid fa-circle-check mr-2"></i>

                {{ session('success') }}

            </div>

        @endif


        @if(session('error'))

            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-6">

                <i class="fa-solid fa-circle-exclamation mr-2"></i>

                {{ session('error') }}

            </div>

        @endif


        @if($errors->any())

            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-6">

                <ul class="list-disc list-inside">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- MOIS ACTUEL --}}

        @if($currentMonthData)

            <div
                id="current-month"
                class="mb-8 scroll-mt-24"
            >

                <div class="bg-white rounded-xl shadow-md border-2 border-blue-500 overflow-hidden">

                    <div class="bg-blue-50 px-5 py-4 border-b">

                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                            <div>

                                <div class="flex items-center gap-2 flex-wrap">

                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-600 text-white">

                                        MOIS ACTUEL

                                    </span>

                                    <h2 class="font-bold text-gray-800">

                                        {{ $currentMonthData['label'] }}

                                    </h2>

                                </div>


                                <p class="text-sm text-gray-500 mt-1">

                                    {{ $currentMonthData['member_count'] }}
                                    membre(s)

                                </p>

                            </div>


                            <div class="flex flex-wrap gap-2 text-xs">

                                <span class="px-3 py-1 rounded-full bg-green-100 text-green-700">

                                    {{ $currentMonthData['paid_count'] }}
                                    payé(s)

                                </span>


                                <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700">

                                    {{ $currentMonthData['pending_count'] }}
                                    en attente

                                </span>


                                <span class="px-3 py-1 rounded-full bg-red-100 text-red-700">

                                    {{ $currentMonthData['unpaid_count'] }}
                                    non payé(s)

                                </span>

                            </div>

                        </div>

                    </div>


                    <div class="divide-y">

                        @foreach(
                            $currentMonthData['members']
                            as $item
                        )

                            @php

                                $contribution =
                                    $item['contribution'];

                                $user =
                                    $item['user'];

                                $status =
                                    $item['status'];

                            @endphp


                            <div class="px-5 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                                <div class="flex items-center gap-3">

                                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">

                                        <i class="fa-solid fa-user text-gray-500"></i>

                                    </div>


                                    <div>

                                        <p class="font-semibold text-gray-800">

                                            {{ $user->name ?? 'Inconnu' }}


                                            @if(
                                                $user &&
                                                $user->id === auth()->id()
                                            )

                                                <span class="text-xs text-blue-600">
                                                    (Moi)
                                                </span>

                                            @endif

                                        </p>


                                        @if($contribution)

                                            <p class="text-xs text-gray-500">

                                                {{ number_format(
                                                    $contribution->amount,
                                                    0,
                                                    ',',
                                                    ' '
                                                ) }}

                                                FCFA


                                                @if(
                                                    $contribution->approvedBy
                                                )

                                                    · Validé par

                                                    {{ $contribution->approvedBy->name }}

                                                @endif

                                            </p>

                                        @endif

                                    </div>

                                </div>


                                <div class="flex items-center gap-3">

                                    @if($status === 'paid')

                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-green-100 text-green-700 text-sm font-medium">

                                            <i class="fa-solid fa-check"></i>

                                            Payé

                                        </span>


                                    @elseif($status === 'pending')

                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-yellow-100 text-yellow-700 text-sm font-medium">

                                            <i class="fa-solid fa-clock"></i>

                                            En attente

                                        </span>


                                    @elseif($status === 'rejected')

                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-red-100 text-red-700 text-sm font-medium">

                                            <i class="fa-solid fa-xmark"></i>

                                            Rejeté

                                        </span>


                                    @else

                                        @if($isTreasurer)

                                            <button
                                                type="button"
                                                onclick="openModalForMember(
                                                    {{ $user->id }},
                                                    '{{ $currentMonthData['month'] }}'
                                                )"
                                                class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm"
                                            >

                                                <i class="fa-solid fa-plus"></i>

                                                Enregistrer

                                            </button>


                                        @elseif(
                                            $user->id === auth()->id()
                                        )

                                            <button
                                                type="button"
                                                onclick="openModalForMember(
                                                    {{ $user->id }},
                                                    '{{ $currentMonthData['month'] }}'
                                                )"
                                                class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm"
                                            >

                                                <i class="fa-solid fa-money-bill"></i>

                                                Signaler mon paiement

                                            </button>


                                        @else

                                            <span class="text-gray-400 text-sm">
                                                —
                                            </span>

                                        @endif

                                    @endif

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

            </div>

        @else

            <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-xl p-4 mb-8">

                Aucune donnée disponible pour le mois actuel.

            </div>

        @endif


        {{-- MOIS PRÉCÉDENTS --}}

        @if($pastMonths->count() > 0)

            <div class="mb-6">

                <button
                    type="button"
                    onclick="toggleSection(
                        'past-months',
                        'past-icon'
                    )"
                    class="w-full flex items-center justify-between bg-white border rounded-xl px-5 py-4 hover:bg-gray-50 transition"
                >

                    <div class="flex items-center gap-3">

                        <i class="fa-solid fa-clock-rotate-left text-gray-500"></i>

                        <div class="text-left">

                            <p class="font-bold text-gray-800">
                                Mois précédents
                            </p>

                            <p class="text-xs text-gray-500">

                                {{ $pastMonths->count() }}

                                mois

                            </p>

                        </div>

                    </div>


                    <i
                        id="past-icon"
                        class="fa-solid fa-chevron-down text-gray-500 transition-transform"
                    ></i>

                </button>


                <div
                    id="past-months"
                    class="hidden mt-4 space-y-4"
                >

                    @foreach($pastMonths as $month)

                        <div class="bg-white rounded-xl border overflow-hidden">

                            <div class="bg-gray-50 px-5 py-4 border-b">

                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                                    <div>

                                        <div class="flex items-center gap-2 flex-wrap">

                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-600">

                                                PASSÉ

                                            </span>

                                            <h2 class="font-bold text-gray-700">

                                                {{ $month['label'] }}

                                            </h2>

                                        </div>

                                    </div>


                                    <div class="flex flex-wrap gap-2 text-xs">

                                        <span class="px-3 py-1 rounded-full bg-green-100 text-green-700">

                                            {{ $month['paid_count'] }}
                                            payé(s)

                                        </span>


                                        <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700">

                                            {{ $month['pending_count'] }}
                                            en attente

                                        </span>


                                        <span class="px-3 py-1 rounded-full bg-red-100 text-red-700">

                                            {{ $month['unpaid_count'] }}
                                            non payé(s)

                                        </span>

                                    </div>

                                </div>

                            </div>


                            <div class="divide-y">

                                @foreach(
                                    $month['members']
                                    as $item
                                )

                                    @php

                                        $contribution =
                                            $item['contribution'];

                                        $user =
                                            $item['user'];

                                        $status =
                                            $item['status'];

                                    @endphp


                                    <div class="px-5 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                                        <div class="flex items-center gap-3">

                                            <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center">

                                                <i class="fa-solid fa-user text-gray-500"></i>

                                            </div>


                                            <div>

                                                <p class="font-semibold text-gray-800">

                                                    {{ $user->name ?? 'Inconnu' }}


                                                    @if(
                                                        $user &&
                                                        $user->id === auth()->id()
                                                    )

                                                        <span class="text-xs text-blue-600">
                                                            (Moi)
                                                        </span>

                                                    @endif

                                                </p>


                                                @if($contribution)

                                                    <p class="text-xs text-gray-500">

                                                        {{ number_format(
                                                            $contribution->amount,
                                                            0,
                                                            ',',
                                                            ' '
                                                        ) }}

                                                        FCFA

                                                    </p>

                                                @endif

                                            </div>

                                        </div>


                                        <div class="flex items-center gap-3">

                                            @if($status === 'paid')

                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-green-100 text-green-700 text-sm">

                                                    <i class="fa-solid fa-check"></i>

                                                    Payé

                                                </span>


                                            @elseif($status === 'pending')

                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-yellow-100 text-yellow-700 text-sm">

                                                    <i class="fa-solid fa-clock"></i>

                                                    En attente

                                                </span>


                                            @elseif($status === 'rejected')

                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-red-100 text-red-700 text-sm">

                                                    <i class="fa-solid fa-xmark"></i>

                                                    Rejeté

                                                </span>


                                            @else

                                                @if($isTreasurer)

                                                    <button
                                                        type="button"
                                                        onclick="openModalForMember(
                                                            {{ $user->id }},
                                                            '{{ $month['month'] }}'
                                                        )"
                                                        class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm"
                                                    >

                                                        <i class="fa-solid fa-plus"></i>

                                                        Enregistrer

                                                    </button>


                                                @elseif(
                                                    $user->id === auth()->id()
                                                )

                                                    <button
                                                        type="button"
                                                        onclick="openModalForMember(
                                                            {{ $user->id }},
                                                            '{{ $month['month'] }}'
                                                        )"
                                                        class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm"
                                                    >

                                                        Signaler mon paiement

                                                    </button>


                                                @else

                                                    <span class="text-gray-400 text-sm">
                                                        —
                                                    </span>

                                                @endif

                                            @endif

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        @endif


        {{-- MOIS FUTURS --}}

        @if($futureMonths->count() > 0)

            <div class="mb-6">

                <button
                    type="button"
                    onclick="toggleSection(
                        'future-months',
                        'future-icon'
                    )"
                    class="w-full flex items-center justify-between bg-white border rounded-xl px-5 py-4 hover:bg-gray-50 transition"
                >

                    <div class="flex items-center gap-3">

                        <i class="fa-solid fa-calendar-plus text-gray-500"></i>

                        <div class="text-left">

                            <p class="font-bold text-gray-800">
                                Mois futurs
                            </p>

                            <p class="text-xs text-gray-500">

                                {{ $futureMonths->count() }}

                                mois à venir

                            </p>

                        </div>

                    </div>


                    <i
                        id="future-icon"
                        class="fa-solid fa-chevron-down text-gray-500 transition-transform"
                    ></i>

                </button>


                <div
                    id="future-months"
                    class="hidden mt-4 space-y-4"
                >

                    @foreach($futureMonths as $month)

                        <div class="bg-white rounded-xl border overflow-hidden">

                            <div class="bg-gray-50 px-5 py-4">

                                <div class="flex items-center gap-2 flex-wrap">

                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-600">

                                        À VENIR

                                    </span>

                                    <h2 class="font-bold text-gray-700">

                                        {{ $month['label'] }}

                                    </h2>

                                </div>


                                <p class="text-sm text-gray-500 mt-2">

                                    Ce mois n'a pas encore commencé.

                                </p>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        @endif

    @else

        <div class="bg-white rounded-xl border p-8 text-center">

            <i class="fa-solid fa-circle-exclamation text-4xl text-gray-400 mb-3"></i>

            <p class="text-gray-600">
                Aucune tontine active actuellement.
            </p>

        </div>

    @endif

</div>


{{-- MODAL --}}

<div
    id="contributionModal"
    class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4"
>

    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden">

        <div class="px-6 py-4 border-b flex items-center justify-between">

            <div>

                <h2 class="text-lg font-bold text-gray-800">

                    Enregistrer une cotisation

                </h2>

                <p class="text-xs text-gray-500 mt-1">

                    Le montant est celui défini par la tontine.

                </p>

            </div>


            <button
                type="button"
                onclick="closeModal()"
                class="text-gray-400 hover:text-gray-600"
            >

                <i class="fa-solid fa-xmark text-xl"></i>

            </button>

        </div>


        <form
            method="POST"
            action="{{ route(
                'admin.collective-contributions.store'
            ) }}"
            enctype="multipart/form-data"
            class="p-6 space-y-5"
        >

            @csrf


            {{-- MEMBRE --}}

            <div>

                <label
                    for="modal_user_id"
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Membre
                </label>


                @if($isTreasurer)

                    <select
                        id="modal_user_id"
                        name="user_id"
                        required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >

                        <option value="">
                            Sélectionner un membre
                        </option>


                        @foreach($users as $user)

                            <option
                                value="{{ $user->id }}"
                            >

                                {{ $user->name }}

                                @if(
                                    $user->id === auth()->id()
                                )

                                    (moi)

                                @endif

                            </option>

                        @endforeach

                    </select>

                @else

                    <input
                        type="hidden"
                        id="modal_user_id"
                        name="user_id"
                        value="{{ auth()->id() }}"
                    >

                    <div class="w-full border border-gray-300 bg-gray-50 rounded-lg px-3 py-2.5">

                        {{ auth()->user()->name }}

                    </div>

                @endif

            </div>


            {{-- MOIS --}}

            <div>

                <label
                    for="modal_month"
                    class="block text-sm font-medium text-gray-700 mb-2"
                >

                    Mois concerné

                </label>


                <select
                    id="modal_month"
                    name="month"
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >

                    <option value="">
                        Sélectionner un mois
                    </option>


                    @foreach(
                        $monthlyContributions
                        as $month
                    )

                        <option
                            value="{{ $month['month'] }}"
                        >

                            {{ $month['label'] }}

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- MONTANT --}}

            <div>

                <label class="block text-sm font-medium text-gray-700 mb-2">

                    Montant

                </label>


                <div class="w-full border border-gray-300 bg-gray-50 rounded-lg px-3 py-2.5 font-semibold text-gray-800">

                    {{ number_format(
                        $activeTontine->collective_amount,
                        0,
                        ',',
                        ' '
                    ) }}

                    FCFA

                </div>

            </div>


            {{-- PREUVE --}}

            <div>

                <label
                    for="proof_path"
                    class="block text-sm font-medium text-gray-700 mb-2"
                >

                    Preuve de paiement

                    <span class="text-gray-400">
                        (facultatif)
                    </span>

                </label>


                <input
                    type="file"
                    id="proof_path"
                    name="proof_path"
                    accept="image/*,.pdf"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2"
                >

            </div>


            {{-- BOUTONS --}}

            <div class="flex justify-end gap-3 pt-3">

                <button
                    type="button"
                    onclick="closeModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                >

                    Annuler

                </button>


                <button
                    type="submit"
                    class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                >

                    Enregistrer

                </button>

            </div>

        </form>

    </div>

</div>


<script>

    function openModal() {

        const modal =
            document.getElementById(
                'contributionModal'
            );

        if (!modal) {
            return;
        }

        modal.classList.remove('hidden');
    }


    function openModalForMember(
        userId,
        month
    ) {

        const modal =
            document.getElementById(
                'contributionModal'
            );

        const userSelect =
            document.getElementById(
                'modal_user_id'
            );

        const monthSelect =
            document.getElementById(
                'modal_month'
            );


        if (!modal) {
            return;
        }


        if (userSelect) {

            userSelect.value =
                userId;

        }


        if (monthSelect) {

            monthSelect.value =
                month;

        }


        modal.classList.remove('hidden');
    }


    function closeModal() {

        const modal =
            document.getElementById(
                'contributionModal'
            );

        if (!modal) {
            return;
        }

        modal.classList.add('hidden');
    }


    document
        .getElementById('contributionModal')
        ?.addEventListener(
            'click',
            function (event) {

                if (
                    event.target === this
                ) {

                    closeModal();

                }

            }
        );


    function toggleSection(
        sectionId,
        iconId
    ) {

        const section =
            document.getElementById(
                sectionId
            );

        const icon =
            document.getElementById(
                iconId
            );


        if (!section) {
            return;
        }


        section.classList.toggle(
            'hidden'
        );


        if (icon) {

            icon.classList.toggle(
                'rotate-180'
            );

        }

    }


    function scrollToCurrentMonth() {

        const currentMonth =
            document.getElementById(
                'current-month'
            );


        if (!currentMonth) {
            return;
        }


        currentMonth.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });

    }

</script>

@endsection