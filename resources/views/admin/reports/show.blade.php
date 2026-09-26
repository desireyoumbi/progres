@extends('layouts.app')

@section('title', 'PROGRÈS - ' . $report->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Barre de navigation / Retour -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center space-x-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-progBlue dark:hover:text-amber-400 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Retour à la liste des rapports</span>
        </a>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.reports.edit', $report) }}" class="px-4 py-2 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 font-medium text-sm hover:bg-amber-100 transition-all flex items-center space-x-1.5">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Modifier</span>
            </a>
            <button onclick="window.print();" class="px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-medium text-sm hover:bg-gray-200 transition-all flex items-center space-x-1.5">
                <i class="fa-solid fa-print"></i>
                <span>Imprimer</span>
            </button>
        </div>
    </div>

    <!-- Document du Rapport -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 md:p-12 space-y-8 transition-colors duration-200">
        
        <!-- En-tête du document -->
        <div class="border-b border-gray-100 dark:border-gray-700 pb-6 text-center space-y-2">
            <span class="text-xs uppercase tracking-widest font-bold text-amber-500">Plateforme PROGRÈS — Compte-rendu officiel</span>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white">{{ $report->title }}</h1>
            <div class="flex items-center justify-center space-x-6 text-sm text-gray-500 dark:text-gray-400 pt-2">
                <span><i class="fa-regular fa-calendar-days mr-1 text-progBlue dark:text-blue-400"></i> Réunion du : <strong>{{ \Carbon\Carbon::parse($report->meeting_date)->translatedFormat('d F Y') }}</strong></span>
                <span><i class="fa-solid fa-user-pen mr-1 text-progBlue dark:text-blue-400"></i> Rédigé par : <strong>{{ $report->author->name ?? 'Inconnu' }}</strong></span>
            </div>
        </div>

        <!-- Corps du rapport -->
        <div class="prose dark:prose-invert max-w-none text-gray-800 dark:text-gray-200 whitespace-pre-line leading-relaxed text-base">
            {!! nl2br(e($report->content)) !!}
        </div>

        <!-- Pied de page du document -->
        <div class="border-t border-gray-100 dark:border-gray-700 pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-400 dark:text-gray-500">
            <p>Enregistré le {{ $report->created_at->format('d/m/Y à H:i') }}</p>
            <p>Association PROGRÈS — Document officiel</p>
        </div>
    </div>
</div>
@endsection