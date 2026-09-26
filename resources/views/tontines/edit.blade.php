@extends('layouts.app')

@section('title', 'Modifier la tontine - PROGRÈS')

@section('content')
<div class="max-w-xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="bg-gradient-to-r from-[#0b5394] to-[#073763] px-6 py-4 text-white flex justify-between items-center">
        <h1 class="font-bold text-lg flex items-center space-x-2">
            <i class="fa-solid fa-pen-to-square text-amber-300"></i>
            <span>Modifier le cycle de tontine</span>
        </h1>
        <a href="{{ route('admin.tontines.index') }}" class="text-xs bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg transition-all text-blue-100">
            &larr; Retour
        </a>
    </div>

    @if($hasContributions)
        <div class="m-6 p-4 bg-amber-50 dark:bg-amber-900/30 border-l-4 border-amber-500 text-amber-700 dark:text-amber-300 text-sm rounded-r-xl">
            <i class="fa-solid fa-triangle-exclamation mr-2"></i>
            Cette tontine possède déjà des cotisations de caisse collective enregistrées. Elle ne peut plus être modifiée.
        </div>
    @endif

    <form action="{{ route('admin.tontines.update', $tontine->id) }}" method="POST" class="p-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Nom du cycle / Session</label>
            <input type="text" name="name" value="{{ old('name', $tontine->name) }}" required {{ $hasContributions ? 'disabled' : '' }}
                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-[#0b5394] focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed">
            @error('name')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Date de début</label>
                <input type="date" name="start_date" value="{{ old('start_date', $tontine->start_date->format('Y-m-d')) }}" required {{ $hasContributions ? 'disabled' : '' }}
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-[#0b5394] focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed">
                @error('start_date')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Date de fin</label>
                <input type="date" name="end_date" value="{{ old('end_date', $tontine->end_date->format('Y-m-d')) }}" required {{ $hasContributions ? 'disabled' : '' }}
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-[#0b5394] focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed">
                @error('end_date')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Montant Tournant (FCFA)</label>
                <input type="number" name="rotating_amount" value="{{ old('rotating_amount', $tontine->rotating_amount) }}" min="0" required {{ $hasContributions ? 'disabled' : '' }}
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-[#0b5394] focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed">
                @error('rotating_amount')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Caisse Collective (FCFA)</label>
                <input type="number" name="collective_amount" value="{{ old('collective_amount', $tontine->collective_amount) }}" min="0" required {{ $hasContributions ? 'disabled' : '' }}
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-[#0b5394] focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed">
                @error('collective_amount')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="pt-4 flex justify-end space-x-3">
            <a href="{{ route('admin.tontines.index') }}" class="px-5 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                Annuler
            </a>
            <button type="submit" {{ $hasContributions ? 'disabled' : '' }} class="px-5 py-2.5 rounded-xl bg-[#0b5394] hover:bg-[#073763] text-white text-sm font-semibold shadow-md transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                Mettre à jour
            </button>
        </div>
    </form>
</div>
@endsection