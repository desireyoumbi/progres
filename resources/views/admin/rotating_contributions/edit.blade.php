<!-- resources/views/admin/rotating_contributions/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Modifier la cotisation rotative - PROGRÈS')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    
    <!-- En-tête -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Modifier la cotisation rotative</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Mettre à jour les informations du versement.</p>
        </div>
        <a href="{{ route('admin.rotating-contributions.index') }}" class="px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-semibold hover:bg-gray-200 transition-all">
            <i class="fa-solid fa-arrow-left mr-1"></i> Retour
        </a>
    </div>

    <!-- Erreurs de validation -->
    @if($errors->any())
        <div class="p-4 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-300 text-sm rounded-r-xl shadow-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulaire de modification -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden p-6">
        <form action="{{ route('admin.rotating-contributions.update', $rotatingContribution->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Cycle de Tontine</label>
                <select name="tontine_id" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 text-sm focus:ring-2 focus:ring-[#0b5394] focus:outline-none">
                    <option value="">Sélectionner une tontine</option>
                    @foreach($tontines as $tontine)
                        <option value="{{ $tontine->id }}" {{ old('tontine_id', $rotatingContribution->tontine_id) == $tontine->id ? 'selected' : '' }}>
                            {{ $tontine->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Membre</label>
                <select name="user_id" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 text-sm focus:ring-2 focus:ring-[#0b5394] focus:outline-none">
                    <option value="">Sélectionner un membre</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $rotatingContribution->user_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Semaine concernée (Date)</label>
                    <input type="date" name="week" value="{{ old('week', \Carbon\Carbon::parse($rotatingContribution->week)->format('Y-m-d')) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 text-sm focus:ring-2 focus:ring-[#0b5394] focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Montant (FCFA)</label>
                    <input type="number" name="amount" min="0" value="{{ old('amount', $rotatingContribution->amount) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 text-sm focus:ring-2 focus:ring-[#0b5394] focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Preuve de paiement (Laisser vide pour conserver l'actuelle)</label>
                @if($rotatingContribution->proof_path)
                    <div class="mb-2 text-xs">
                        <a href="{{ asset('storage/' . $rotatingContribution->proof_path) }}" target="_blank" class="text-blue-500 hover:underline flex items-center space-x-1">
                            <i class="fa-solid fa-file-arrow-down"></i>
                            <span>Voir la preuve actuelle</span>
                        </a>
                    </div>
                @endif
                <input type="file" name="proof_path" accept="image/*,pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-[#0b5394] dark:file:bg-gray-700 dark:file:text-gray-300 hover:file:bg-blue-100">
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="w-full py-3 rounded-xl bg-[#0b5394] hover:bg-[#073763] text-white text-sm font-semibold shadow-md transition-all">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection