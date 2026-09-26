<!-- resources/views/admin/tontines/index.blade.php -->
@extends('layouts.app')

@section('title', 'Gestion des Tontines - PROGRÈS')

@section('content')
<div class="space-y-6">
    
    <!-- En-tête de page -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Cycles de Tontines & Caisses</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Gérez les sessions de tontines, les cotisations tournantes et les caisses collectives.</p>
        </div>
        <!-- Bouton pour ouvrir le modal de création : visible uniquement pour président/trésorier -->
        @can('create', App\Models\Tontine::class)
            <button onclick="document.getElementById('createTontineModal').classList.remove('hidden')" class="bg-[#0b5394] hover:bg-[#073763] text-white px-5 py-2.5 rounded-xl shadow-md transition-all flex items-center space-x-2 text-sm w-fit font-semibold">
                <i class="fa-solid fa-circle-plus text-amber-300"></i>
                <span>Créer une tontine</span>
            </button>
        @endcan
    </div>

    <!-- Messages Flash -->
    @if(session('success'))
        <div class="p-4 bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-300 text-sm rounded-r-xl shadow-sm">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-300 text-sm rounded-r-xl shadow-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tableau des tontines -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider border-b border-gray-100 dark:border-gray-700">
                        <th class="py-4 px-6 font-bold">Nom du Cycle</th>
                        <th class="py-4 px-6 font-bold">Période</th>
                        <th class="py-4 px-6 font-bold">Montant Tournant</th>
                        <th class="py-4 px-6 font-bold">Caisse Collective</th>
                        <th class="py-4 px-6 font-bold">Statut</th>
                        <th class="py-4 px-6 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm text-gray-700 dark:text-gray-300">
                    @forelse($tontines as $tontine)
                        <tr class="hover:bg-blue-50/30 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="py-4 px-6 font-medium text-gray-900 dark:text-white flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-gray-700 text-[#0b5394] dark:text-amber-300 flex items-center justify-center font-bold text-xs shadow-inner">
                                    <i class="fa-solid fa-coins"></i>
                                </div>
                                <span>{{ $tontine->name }}</span>
                            </td>
                            <td class="py-4 px-6 text-xs text-gray-500 dark:text-gray-400">
                                Du {{ \Carbon\Carbon::parse($tontine->start_date)->format('d/m/Y') }}<br>
                                Au {{ \Carbon\Carbon::parse($tontine->end_date)->format('d/m/Y') }}
                            </td>
                            <td class="py-4 px-6 font-semibold text-[#0b5394] dark:text-blue-400">
                                {{ number_format($tontine->rotating_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="py-4 px-6 font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ number_format($tontine->collective_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="py-4 px-6">
                                @if($tontine->status === 'active')
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full border bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-700">
                                        Actif
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full border bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                                        Clôturé
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right space-x-1">
                                <!-- Bouton Modifier : renvoie vers la vraie page d'édition (plus de modal dupliqué) -->
                                @can('update', $tontine)
                                    <a href="{{ route('admin.tontines.edit', $tontine->id) }}" class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-gray-700 rounded-lg transition-colors inline-block" title="Modifier">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <!-- Bouton Clôturer -->
                                    @if($tontine->status === 'active')
                                        <form action="{{ route('admin.tontines.close', $tontine->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Voulez-vous vraiment clôturer ce cycle de tontine ?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="p-2 text-amber-500 hover:bg-amber-50 dark:hover:bg-gray-700 rounded-lg transition-colors" title="Clôturer">
                                                <i class="fa-solid fa-lock"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400 dark:text-gray-500">
                                Aucun cycle de tontine enregistré pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($tontines->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                {{ $tontines->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Création Tontine (inchangé) -->
@can('create', App\Models\Tontine::class)
<div id="createTontineModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 max-w-lg w-full overflow-hidden">
        <div class="bg-gradient-to-r from-[#0b5394] to-[#073763] px-6 py-4 text-white flex justify-between items-center">
            <h3 class="font-bold text-lg flex items-center space-x-2">
                <i class="fa-solid fa-circle-plus text-amber-300"></i>
                <span>Créer un nouveau cycle de tontine</span>
            </h3>
            <button onclick="document.getElementById('createTontineModal').classList.add('hidden')" class="text-white/80 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('admin.tontines.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Nom du cycle / Session</label>
                <input type="text" name="name" required placeholder="Ex: Tontine Mensuelle - Session 2026"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-[#0b5394] focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Date de début</label>
                    <input type="date" name="start_date" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-[#0b5394] focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Date de fin</label>
                    <input type="date" name="end_date" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-[#0b5394] focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Montant Tournant (FCFA)</label>
                    <input type="number" name="rotating_amount" min="0" required placeholder="Ex: 50000"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-[#0b5394] focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Caisse Collective (FCFA)</label>
                    <input type="number" name="collective_amount" min="0" required placeholder="Ex: 5000"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-[#0b5394] focus:outline-none">
                </div>
            </div>

            <div class="pt-4 flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('createTontineModal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                    Annuler
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0b5394] hover:bg-[#073763] text-white text-sm font-semibold shadow-md transition-all">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

@endsection