<!-- resources/views/admin/reports/index.blade.php -->
@extends('layouts.app')

@section('title', 'PROGRÈS - Gestion des Rapports')

@section('content')
<div class="space-y-6">
    <!-- En-tête de page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center space-x-2">
                <i class="fa-solid fa-file-lines text-amber-500"></i>
                <span>Rapports de Réunion</span>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Consultez, rédigez et gérez les comptes-rendus des réunions de l'association.</p>
        </div>
        <a href="{{ route('admin.reports.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-progBlue hover:bg-progDarkBlue text-white font-medium text-sm shadow-md transition-all space-x-2">
            <i class="fa-solid fa-plus text-amber-300"></i>
            <span>Rédiger un rapport</span>
        </a>
    </div>

    <!-- Message de succès -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm flex items-center space-x-3 shadow-sm">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Tableau / Liste des rapports -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-xs uppercase font-semibold tracking-wider border-b border-gray-100 dark:border-gray-700">
                        <th class="py-4 px-6">Titre du Rapport</th>
                        <th class="py-4 px-6">Date de la Réunion</th>
                        <th class="py-4 px-6">Rédacteur</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                    @forelse($reports as $report)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="py-4 px-6 font-medium text-gray-900 dark:text-white">
                                <a href="{{ route('admin.reports.show', $report) }}" class="hover:text-progBlue dark:hover:text-amber-400 transition-colors flex items-center space-x-2">
                                    <i class="fa-solid fa-file-pdf text-amber-500 text-base"></i>
                                    <span>{{ $report->title }}</span>
                                </a>
                            </td>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">
                                <i class="fa-regular fa-calendar mr-1.5 text-gray-400"></i>
                                {{ \Carbon\Carbon::parse($report->meeting_date)->translatedFormat('d F Y') }}
                            </td>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300">
                                <div class="flex items-center space-x-2">
                                    <div class="w-7 h-7 rounded-full bg-progBlue/10 dark:bg-blue-900/40 text-progBlue dark:text-blue-300 flex items-center justify-center font-bold text-xs">
                                        {{ substr($report->author->name ?? 'A', 0, 1) }}
                                    </div>
                                    <span>{{ $report->author->name ?? 'Inconnu' }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('admin.reports.show', $report) }}" class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-progBlue dark:text-blue-300 hover:bg-blue-100 transition-colors" title="Voir">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                @if(in_array(auth()->user()->role, ['secretary', 'treasurer']))
                                    <a href="{{ $report->whatsapp_share_url }}" target="_blank" rel="noopener" class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 transition-colors" title="Partager sur WhatsApp">
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </a>
                                @endif

                                <a href="{{ route('admin.reports.edit', $report) }}" class="p-2 rounded-lg bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 hover:bg-amber-100 transition-colors" title="Modifier">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.reports.destroy', $report) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rapport ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 hover:bg-rose-100 transition-colors" title="Supprimer">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-gray-400 dark:text-gray-500">
                                <i class="fa-regular fa-folder-open text-4xl mb-3 block"></i>
                                Aucun rapport de réunion enregistré pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($reports->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>
@endsection