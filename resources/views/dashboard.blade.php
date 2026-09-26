<!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Tableau de bord - PROGRÈS')

@section('content')
<div class="space-y-8">
    
    <!-- En-tête de Bienvenue dynamique selon le rôle -->
    <div class="bg-gradient-to-br from-[#0b5394] to-[#073763] dark:from-gray-800 dark:to-gray-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden flex flex-col md:flex-row justify-between items-center border border-blue-900/10 dark:border-gray-700">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-amber-400 opacity-10 rounded-full blur-2xl"></div>
        <div class="z-10 mb-4 md:mb-0">
            <span class="inline-block px-3 py-1 bg-amber-400/20 text-amber-300 border border-amber-400/30 rounded-full text-xs font-bold uppercase tracking-wider mb-2">
                @php
                    $roleLabels = [
                        'president' => 'Président(e)',
                        'treasurer' => 'Trésorier(ère)',
                        'secretary' => 'Secrétaire',
                        'member' => 'Membre Actif'
                    ];
                @endphp
                {{ $roleLabels[auth()->user()->role] ?? 'Membre' }}
            </span>
            <h1 class="text-2xl sm:text-3xl font-bold mb-2">Heureux de vous revoir, {{ auth()->user()->name }} !</h1>
            <p class="text-blue-100 dark:text-gray-300 text-sm max-w-xl">
                @if(auth()->user()->isAdmin())
                    Bienvenue sur le panneau de contrôle du bureau exécutif. Gérez les membres, suivez les caisses et supervisez les cycles de tontines.
                @else
                    Suivez l'évolution de vos cotisations, consultez vos tontines en cours et restez connectés aux actualités de l'association.
                @endif
            </p>
        </div>
        <div class="z-10 bg-white/10 dark:bg-gray-800/80 border border-white/20 dark:border-gray-700 backdrop-blur-md px-6 py-4 rounded-2xl text-center shadow-lg">
            <i class="fa-solid fa-phone text-amber-300 text-2xl mb-1"></i>
            <p class="text-xs text-blue-200 dark:text-gray-400 uppercase font-semibold">Identifiant / Téléphone</p>
            <p class="text-base font-bold text-white dark:text-gray-200">{{ auth()->user()->phone }}</p>
        </div>
    </div>

    <!-- SECTION SPÉCIFIQUE : BUREAU EXÉCUTIF (Président, Trésorier, Secrétaire) -->
    @if(auth()->user()->isAdmin())
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4 flex items-center space-x-2">
                <i class="fa-solid fa-gauge-high text-[#0b5394] dark:text-amber-400"></i>
                <span>Panneau d'Administration du Bureau</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Gestion des Membres -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 flex flex-col justify-between transition-all hover:shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-50 dark:bg-gray-700 text-[#0b5394] dark:text-amber-300 rounded-xl flex items-center justify-center text-xl font-bold">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-1 rounded-full">Actifs</span>
                    </div>
                    <div>
                        <h3 class="text-gray-500 dark:text-gray-400 text-xs font-bold uppercase">Total Membres</h3>
                        <p class="text-2xl font-extrabold text-gray-800 dark:text-white mt-1">{{\App\Models\User::count()}}</p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <span class="text-xs text-gray-400 dark:text-gray-500">Registre global</span>
                        <a href="#" class="text-xs font-bold text-[#0b5394] dark:text-amber-400 hover:underline">Gérer &rarr;</a>
                    </div>
                </div>

                <!-- Gestion des Tontines -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 flex flex-col justify-between transition-all hover:shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-amber-50 dark:bg-gray-700 text-amber-600 dark:text-amber-300 rounded-xl flex items-center justify-center text-xl font-bold">
                            <i class="fa-solid fa-coins"></i>
                        </div>
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 dark:bg-blue-900/30 px-2.5 py-1 rounded-full">Cycles</span>
                    </div>
                    <div>
                        <h3 class="text-gray-500 dark:text-gray-400 text-xs font-bold uppercase">Tontines & Caisses</h3>
                        <p class="text-2xl font-extrabold text-gray-800 dark:text-white mt-1">{{\App\Models\Tontine::count()}}</p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <span class="text-xs text-gray-400 dark:text-gray-500">Sessions en cours</span>
                        <a href="#" class="text-xs font-bold text-[#0b5394] dark:text-amber-400 hover:underline">Configurer &rarr;</a>
                    </div>
                </div>

                <!-- Rôles spécifiques selon le membre connecté du bureau -->
                @if(auth()->user()->isPresident())
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-50 dark:bg-gray-700 text-purple-600 dark:text-purple-300 rounded-xl flex items-center justify-center text-xl font-bold">
                            <i class="fa-solid fa-chess-king"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-gray-500 dark:text-gray-400 text-xs font-bold uppercase">Supervision</h3>
                        <p class="text-sm font-bold text-gray-800 dark:text-white mt-1">Validation globale</p>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">Accès présidentiel complet</p>
                </div>
                @endif

                @if(auth()->user()->isTreasurer())
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-emerald-50 dark:bg-gray-700 text-emerald-600 dark:text-emerald-300 rounded-xl flex items-center justify-center text-xl font-bold">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-gray-500 dark:text-gray-400 text-xs font-bold uppercase">Trésorerie</h3>
                        <p class="text-sm font-bold text-gray-800 dark:text-white mt-1">Gestion des fonds</p>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">Validation des versements</p>
                </div>
                @endif

                @if(auth()->user()->isSecretary())
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-sky-50 dark:bg-gray-700 text-sky-600 dark:text-sky-300 rounded-xl flex items-center justify-center text-xl font-bold">
                            <i class="fa-solid fa-file-pen"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-gray-500 dark:text-gray-400 text-xs font-bold uppercase">Secrétariat</h3>
                        <p class="text-sm font-bold text-gray-800 dark:text-white mt-1">Procès-verbaux & listes</p>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">Registre des présences</p>
                </div>
                @endif
            </div>
        </div>
    @endif

    <!-- SECTION SPÉCIFIQUE : MEMBRE STANDARD -->
    @if(!auth()->user()->isAdmin())
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4 flex items-center space-x-2">
                <i class="fa-solid fa-chart-pie text-[#0b5394] dark:text-amber-400"></i>
                <span>Mon Suivi Personnel</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Mes Tontines -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 flex items-center space-x-4">
                    <div class="w-14 h-14 bg-amber-50 dark:bg-gray-700 text-amber-600 dark:text-amber-300 rounded-2xl flex items-center justify-center text-2xl font-bold shadow-inner">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-400 font-bold uppercase">Tontines Inscrites</p>
                        <p class="text-xl font-extrabold text-gray-800 dark:text-white">{{ auth()->user()->tontines()->count() }}</p>
                    </div>
                </div>

                <!-- Mes Contributions -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 flex items-center space-x-4">
                    <div class="w-14 h-14 bg-blue-50 dark:bg-gray-700 text-[#0b5394] dark:text-blue-300 rounded-2xl flex items-center justify-center text-2xl font-bold shadow-inner">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-400 font-bold uppercase">Total Versé</p>
                        <p class="text-xl font-extrabold text-gray-800 dark:text-white">0 XAF</p>
                    </div>
                </div>

                <!-- Prochaine Réunion -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 flex items-center space-x-4">
                    <div class="w-14 h-14 bg-emerald-50 dark:bg-gray-700 text-emerald-600 dark:text-emerald-300 rounded-2xl flex items-center justify-center text-2xl font-bold shadow-inner">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-400 font-bold uppercase">Prochaine Réunion</p>
                        <p class="text-sm font-bold text-gray-800 dark:text-white mt-1">À planifier par le bureau</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection