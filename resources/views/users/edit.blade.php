<!-- resources/views/admin/users/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Modifier le membre - PROGRÈS')

@section('content')
<div class="max-w-xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="bg-gradient-to-r from-[#0b5394] to-[#073763] px-6 py-4 text-white flex justify-between items-center">
        <h1 class="font-bold text-lg flex items-center space-x-2">
            <i class="fa-solid fa-user-pen text-amber-300"></i>
            <span>Modifier les informations : {{ $user->name }}</span>
        </h1>
        <a href="{{ route('admin.users.index') }}" class="text-xs bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg transition-all text-blue-100">
            &larr; Retour
        </a>
    </div>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="p-6 space-y-5">
        @csrf
        @method('PUT')

        <!-- Nom complet -->
        <div>
            <label for="name" class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Nom Complet</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required 
                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-[#0b5394] focus:outline-none">
            @error('name')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Téléphone (ID principal) -->
        <div>
            <label for="phone" class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Numéro de Téléphone (Identifiant)</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" required
                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-[#0b5394] focus:outline-none">
            @error('phone')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email (Optionnel) -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Adresse Email <span class="text-gray-400 font-normal">(Optionnel)</span></label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-[#0b5394] focus:outline-none">
            @error('email')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Rôle dans l'association -->
        <div>
            <label for="role" class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Rôle dans l'association</label>
            <select name="role" id="role" required
                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-[#0b5394] focus:outline-none">
                <option value="member" {{ old('role', $user->role) == 'member' ? 'selected' : '' }}>Membre</option>
                <option value="president" {{ old('role', $user->role) == 'president' ? 'selected' : '' }}>Président(e)</option>
                <option value="treasurer" {{ old('role', $user->role) == 'treasurer' ? 'selected' : '' }}>Trésorier(ère)</option>
                <option value="secretary" {{ old('role', $user->role) == 'secretary' ? 'selected' : '' }}>Secrétaire</option>
            </select>
            @error('role')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Mot de passe (Optionnel en modification) -->
        <div>
            <label for="password" class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Nouveau mot de passe <span class="text-gray-400 font-normal">(Laisser vide pour ne pas modifier)</span></label>
            <div class="relative">
                <input type="password" name="password" id="password"
                    class="w-full px-4 py-2.5 pr-11 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-[#0b5394] focus:outline-none">
                <button type="button" onclick="togglePassword('password', this)" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
            @error('password')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirmation du nouveau mot de passe -->
        <div>
            <label for="password_confirmation" class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-300 mb-1">Confirmer le nouveau mot de passe</label>
            <div class="relative">
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="w-full px-4 py-2.5 pr-11 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-[#0b5394] focus:outline-none">
                <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
        </div>

        <!-- Bouton de soumission -->
        <div class="pt-4 flex justify-end space-x-3">
            <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                Annuler
            </a>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0b5394] hover:bg-[#073763] text-white text-sm font-semibold shadow-md transition-all flex items-center space-x-2">
                <i class="fa-solid fa-floppy-disk text-amber-300"></i>
                <span>Mettre à jour</span>
            </button>
        </div>
    </form>
</div>

<script>
    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endsection