<!-- resources/views/admin/users/create.blade.php -->
@extends('layouts.app')

@section('title', 'Ajouter un membre - PROGRÈS')

@section('content')
<div class="max-w-xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="bg-gradient-to-r from-[#0b5394] to-[#073763] px-6 py-4 text-white flex justify-between items-center">
        <h1 class="font-bold text-lg flex items-center space-x-2">
            <i class="fa-solid fa-user-plus text-amber-300"></i>
            <span>Enregistrer un nouveau membre</span>
        </h1>
        <a href="{{ route('admin.users.index') }}" class="text-xs bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg transition-all text-blue-100">
            &larr; Retour
        </a>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-5">
        @csrf

        <!-- Nom complet -->
        <div>
            <label for="name" class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Nom Complet</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-[#0b5394] focus:outline-none">
            @error('name')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Téléphone (ID principal) -->
        <div>
            <label for="phone" class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Numéro de Téléphone (Identifiant)</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required placeholder="Ex: 690000000"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-[#0b5394] focus:outline-none">
            @error('phone')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email (Optionnel) -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Adresse Email <span class="text-gray-400 font-normal">(Optionnel)</span></label>
            <input type="email" name="email" id="email" value="{{ old('email') }}"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-[#0b5394] focus:outline-none">
            @error('email')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Rôle dans l'association -->