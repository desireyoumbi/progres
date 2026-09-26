@extends('layouts.app')

@section('title', 'PROGRÈS - Rédiger un Rapport')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center space-x-2">
            <i class="fa-solid fa-pen-nib text-amber-500"></i>
            <span>Nouveau Rapport de Réunion</span>
        </h1>
        <a href="{{ route('admin.reports.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            <i class="fa-solid fa-arrow-left mr-1">️ Retour à la liste</i>
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
        <form action="{{ route('admin.reports.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Titre du rapport -->
            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Titre du Rapport</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="Ex: Compte-rendu de la réunion du bureau - Septembre 2026" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-progBlue focus:outline-none transition-all">
                @error('title')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date de la réunion -->
            <div>
                <label for="meeting_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Date de la Réunion</label>
                <input type="date" name="meeting_date" id="meeting_date" value="{{ old('meeting_date', date('Y-m-d')) }}" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-progBlue focus:outline-none transition-all">
                @error('meeting_date')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contenu détaillé -->
            <div>
                <label for="content" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Compte-rendu / Contenu Détaillé</label>
                <textarea name="content" id="content" rows="8" placeholder="Rédigez les points abordés, les décisions prises, les présences et les résolutions financières..." required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-progBlue focus:outline-none transition-all">{{ old('content') }}</textarea>
                @error('content')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('admin.reports.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm font-medium transition-all">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-progBlue hover:bg-progDarkBlue text-white font-medium text-sm shadow-md transition-all">
                    Enregistrer le rapport
                </button>
            </div>
        </form>
    </div>
</div>
@endsection