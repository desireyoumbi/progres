<!-- resources/views/admin/users/index.blade.php -->
@extends('layouts.app')

@section('title', 'Gestion des Membres - PROGRÈS')

@section('content')
<div class="space-y-6">
    
    <!-- En-tête de page -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Gestion des Membres</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Liste de tous les participants inscrits et des membres du bureau exécutif.</p>
        </div>
        <!-- Lien vers le formulaire de création (Accessible si Président ou selon vos règles) -->
        @if(strtolower(auth()->user()->role) === 'president')
            <a href="{{ route('admin.users.create') }}" class="bg-[#0b5394] hover:bg-[#073763] text-white px-5 py-2.5 rounded-xl shadow-md transition-all flex items-center space-x-2 text-sm w-fit font-semibold">
                <i class="fa-solid fa-user-plus text-amber-300"></i>
                <span>Ajouter un membre</span>
            </a>
        @endif
    </div>

    <!-- Messages Flash -->
    @if(session('success'))
        <div class="p-4 bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-300 text-sm rounded-r-xl shadow-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-300 text-sm rounded-r-xl shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Tableau des membres -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider border-b border-gray-100 dark:border-gray-700">
                        <th class="py-4 px-6 font-bold">Nom Complet</th>
                        <th class="py-4 px-6 font-bold">Téléphone (ID)</th>
                        <th class="py-4 px-6 font-bold">Email</th>
                        <th class="py-4 px-6 font-bold">Rôle</th>
                        <th class="py-4 px-6 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm text-gray-700 dark:text-gray-300">
                    @foreach($users as $user)
                        <tr class="hover:bg-blue-50/30 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="py-4 px-6 font-medium text-gray-900 dark:text-white flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-gray-700 text-[#0b5394] dark:text-amber-300 flex items-center justify-center font-bold text-xs shadow-inner">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                                <span>{{ $user->name }}</span>
                            </td>
                            <td class="py-4 px-6">{{ $user->phone }}</td>
                            <td class="py-4 px-6 text-gray-500 dark:text-gray-400">{{ $user->email ?? 'Non renseigné' }}</td>
                            <td class="py-4 px-6">
                                @php
                                    $badgeColors = [
                                        'president' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-700',
                                        'treasurer' => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700',
                                        'secretary' => 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-700',
                                        'member' => 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600',
                                    ];
                                    $roleLabels = [
                                        'president' => 'Président(e)',
                                        'treasurer' => 'Trésorier(ère)',
                                        'secretary' => 'Secrétaire',
                                        'member' => 'Membre',
                                    ];
                                @endphp
                                <span class="px-3 py-1 text-xs font-semibold rounded-full border {{ $badgeColors[$user->role] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $roleLabels[$user->role] ?? $user->role }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-1">
                                <!-- Bouton Modifier : Affiché uniquement si c'est son propre profil OU si l'utilisateur connecté est le président -->
                                @if(auth()->id() === $user->id || strtolower(auth()->user()->role) === 'president')
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-gray-700 rounded-lg transition-colors inline-block" title="Modifier">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                @endif

                                <!-- Bouton Supprimer : Affiché uniquement si le user connecté est le président ET qu'il ne supprime pas son propre compte -->
                                @if(strtolower(auth()->user()->role) === 'president')
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Voulez-vous vraiment supprimer ce membre ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-gray-700 rounded-lg transition-colors" title="Supprimer">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400 italic px-2">Compte actuel</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection