@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- En-tête --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Caisse individuelle
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Effectuez des dépôts librement, à la date de votre choix.
            </p>
        </div>

        <button
            onclick="openContributionModal()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition"
        >
            + Déclarer un dépôt
        </button>

    </div>


    {{-- Messages --}}
    @if(session('success'))
        <div class="mb-5 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif


    {{-- Tontine active --}}
    @if(!$activeTontine)

        <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-lg p-4">
            Aucune tontine active pour le moment.
        </div>

    @else

        {{-- Résumé personnel --}}
        @if(!$isTreasurer)

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">

                <p class="text-sm text-gray-500">
                    Total de mes dépôts validés
                </p>

                <p class="text-3xl font-bold text-gray-800 mt-1">
                    {{ number_format($myTotalPaid, 0, ',', ' ') }}
                    FCFA
                </p>

            </div>

        @endif


        {{-- Filtres du trésorier --}}
        @if($isTreasurer)

            <form
                method="GET"
                action="{{ route('admin.individual-contributions.index') }}"
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6"
            >

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    {{-- Tontine --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tontine
                        </label>

                        <select
                            name="tontine_id"
                            class="w-full border-gray-300 rounded-lg"
                        >
                            <option value="">
                                Toutes les tontines
                            </option>

                            @foreach($tontines as $tontine)
                                <option
                                    value="{{ $tontine->id }}"
                                    @selected(request('tontine_id') == $tontine->id)
                                >
                                    {{ $tontine->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Statut --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Statut
                        </label>

                        <select
                            name="status"
                            class="w-full border-gray-300 rounded-lg"
                        >
                            <option value="">
                                Tous les statuts
                            </option>

                            <option
                                value="pending"
                                @selected(request('status') === 'pending')
                            >
                                En attente
                            </option>

                            <option
                                value="approved"
                                @selected(request('status') === 'approved')
                            >
                                Payé
                            </option>

                            <option
                                value="rejected"
                                @selected(request('status') === 'rejected')
                            >
                                Rejeté
                            </option>
                        </select>
                    </div>


                    {{-- Bouton --}}
                    <div class="flex items-end">

                        <button
                            type="submit"
                            class="w-full bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg"
                        >
                            Filtrer
                        </button>

                    </div>

                </div>

            </form>

        @endif


        {{-- Liste des dépôts --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

            <div class="px-5 py-4 border-b border-gray-200">

                <h2 class="font-semibold text-gray-800">
                    {{ $isTreasurer ? 'Tous les dépôts' : 'Mes dépôts' }}
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Chaque dépôt est enregistré indépendamment avec sa date exacte.
                </p>

            </div>


            @if($contributions->count())

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-gray-50 text-gray-600">

                            <tr>

                                @if($isTreasurer)
                                    <th class="text-left px-5 py-3 font-medium">
                                        Membre
                                    </th>
                                @endif

                                <th class="text-left px-5 py-3 font-medium">
                                    Date
                                </th>

                                <th class="text-left px-5 py-3 font-medium">
                                    Montant
                                </th>

                                <th class="text-left px-5 py-3 font-medium">
                                    Preuve
                                </th>

                                <th class="text-left px-5 py-3 font-medium">
                                    Statut
                                </th>

                                @if($isTreasurer)
                                    <th class="text-right px-5 py-3 font-medium">
                                        Actions
                                    </th>
                                @endif

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100">

                            @foreach($contributions as $contribution)

                                <tr class="hover:bg-gray-50">

                                    @if($isTreasurer)

                                        <td class="px-5 py-4">

                                            <div class="font-medium text-gray-800">
                                                {{ $contribution->user->name }}
                                            </div>

                                        </td>

                                    @endif


                                    {{-- Date --}}
                                    <td class="px-5 py-4 text-gray-700">

                                        {{ $contribution->payment_date->format('d/m/Y') }}

                                    </td>


                                    {{-- Montant --}}
                                    <td class="px-5 py-4">

                                        <span class="font-semibold text-gray-800">
                                            {{ number_format($contribution->amount, 0, ',', ' ') }}
                                            FCFA
                                        </span>

                                    </td>


                                    {{-- Preuve --}}
                                    <td class="px-5 py-4">

                                        @if($contribution->proof_path)

                                            <a
                                                href="{{ Storage::url($contribution->proof_path) }}"
                                                target="_blank"
                                                class="text-blue-600 hover:underline"
                                            >
                                                Voir la preuve
                                            </a>

                                        @else

                                            <span class="text-gray-400">
                                                Aucune
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Statut --}}
                                    <td class="px-5 py-4">

                                        @if($contribution->status === 'approved')

                                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                ✓ Payé
                                            </span>

                                        @elseif($contribution->status === 'pending')

                                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                                ⏳ En attente
                                            </span>

                                        @else

                                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                ✕ Rejeté
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Actions trésorier --}}
                                    @if($isTreasurer)

                                        <td class="px-5 py-4">

                                            <div class="flex justify-end gap-2">

                                                @if($contribution->status === 'pending')

                                                    <form
                                                        method="POST"
                                                        action="{{ route('admin.individual-contributions.approve', $contribution) }}"
                                                    >
                                                        @csrf
                                                        @method('PATCH')

                                                        <button
                                                            type="submit"
                                                            class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-medium"
                                                        >
                                                            Valider
                                                        </button>
                                                    </form>


                                                    <form
                                                        method="POST"
                                                        action="{{ route('admin.individual-contributions.reject', $contribution) }}"
                                                    >
                                                        @csrf
                                                        @method('PATCH')

                                                        <button
                                                            type="submit"
                                                            class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-medium"
                                                        >
                                                            Rejeter
                                                        </button>
                                                    </form>

                                                @endif

                                            </div>

                                        </td>

                                    @endif

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                {{-- Pagination --}}
                <div class="px-5 py-4 border-t border-gray-200">
                    {{ $contributions->links() }}
                </div>


            @else

                <div class="p-10 text-center">

                    <div class="text-4xl mb-3">
                        💰
                    </div>

                    <h3 class="font-semibold text-gray-800">
                        Aucun dépôt enregistré
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Vous pouvez effectuer votre premier dépôt quand vous le souhaitez.
                    </p>

                </div>

            @endif

        </div>

    @endif

</div>


{{-- ========================================================= --}}
{{-- MODAL : DÉCLARER UN DÉPÔT --}}
{{-- ========================================================= --}}

<div
    id="contributionModal"
    class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 px-4"
>

    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg">

        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">

            <div>

                <h2 class="text-lg font-semibold text-gray-800">
                    Déclarer un dépôt
                </h2>

                <p class="text-sm text-gray-500">
                    Enregistrez un versement effectué à la date choisie.
                </p>

            </div>

            <button
                type="button"
                onclick="closeContributionModal()"
                class="text-gray-400 hover:text-gray-600 text-xl"
            >
                ×
            </button>

        </div>


        <form
            method="POST"
            action="{{ route('admin.individual-contributions.store') }}"
            enctype="multipart/form-data"
            class="p-6 space-y-5"
        >

            @csrf


            {{-- Membre : uniquement trésorier --}}
            @if($isTreasurer)

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Membre
                    </label>

                    <select
                        name="user_id"
                        required
                        class="w-full border-gray-300 rounded-lg"
                    >

                        <option value="">
                            Sélectionner un membre
                        </option>

                        @foreach($users as $user)

                            <option value="{{ $user->id }}">
                                {{ $user->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

            @endif


            {{-- Date --}}
            <div>

                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Date du dépôt
                </label>

                <input
                    type="date"
                    name="payment_date"
                    value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                    min="{{ $activeTontine?->start_date?->format('Y-m-d') }}"
                    max="{{ $activeTontine?->end_date?->format('Y-m-d') }}"
                    required
                    class="w-full border-gray-300 rounded-lg"
                >

            </div>


            {{-- Montant --}}
            <div>

                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Montant
                </label>

                <div class="relative">

                    <input
                        type="number"
                        name="amount"
                        min="1"
                        step="1"
                        value="{{ old('amount') }}"
                        placeholder="Ex : 10000"
                        required
                        class="w-full border-gray-300 rounded-lg pr-16"
                    >

                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">
                        FCFA
                    </span>

                </div>

            </div>


            {{-- Preuve --}}
            <div>

                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Preuve <span class="text-gray-400">(facultatif)</span>
                </label>

                <input
                    type="file"
                    name="proof_path"
                    accept=".jpg,.jpeg,.png,.pdf"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                >

                <p class="text-xs text-gray-400 mt-1">
                    JPG, PNG ou PDF — 2 Mo maximum.
                </p>

            </div>


            {{-- Boutons --}}
            <div class="flex justify-end gap-3 pt-2">

                <button
                    type="button"
                    onclick="closeContributionModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                >
                    Annuler
                </button>

                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium"
                >
                    Enregistrer
                </button>

            </div>

        </form>

    </div>

</div>


<script>

    function openContributionModal() {

        const modal =
            document.getElementById('contributionModal');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

    }


    function closeContributionModal() {

        const modal =
            document.getElementById('contributionModal');

        modal.classList.remove('flex');
        modal.classList.add('hidden');

    }


    // Fermer en cliquant sur l'arrière-plan
    document
        .getElementById('contributionModal')
        .addEventListener('click', function(event) {

            if (event.target === this) {
                closeContributionModal();
            }

        });

</script>

@endsection