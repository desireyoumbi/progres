@extends('layouts.app')

@section('title', 'Changer mon mot de passe')

@section('content')

<div class="max-w-xl mx-auto py-8 px-4">

```
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 sm:p-8">

    <!-- En-tête -->
    <div class="text-center mb-8">
        <div class="w-14 h-14 mx-auto rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 flex items-center justify-center mb-4">
            <i class="fa-solid fa-lock text-xl"></i>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Changement de mot de passe
        </h1>

        <p class="mt-2 text-sm leading-6 text-gray-500 dark:text-gray-400">
            Pour votre première connexion, vous devez modifier le mot de passe qui vous a été attribué.
        </p>
    </div>

    <!-- Erreurs -->
    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i>

                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Formulaire -->
    <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Nouveau mot de passe -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                Nouveau mot de passe
            </label>

            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                    <i class="fa-solid fa-lock text-gray-400"></i>
                </div>

                <input
                    type="password"
                    name="password"
                    id="password"
                    required
                    autocomplete="new-password"
                    placeholder="Entrez votre nouveau mot de passe"
                    class="w-full h-12 pl-11 pr-12 rounded-xl border border-gray-300 dark:border-gray-600
                           bg-gray-50 dark:bg-gray-700/70
                           text-gray-900 dark:text-white
                           placeholder-gray-400 dark:placeholder-gray-500
                           focus:bg-white dark:focus:bg-gray-700
                           focus:border-progBlue focus:ring-2 focus:ring-progBlue/20
                           transition-all duration-200 outline-none"
                >

                <button
                    type="button"
                    onclick="togglePassword('password', 'password-eye')"
                    class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
                    title="Afficher le mot de passe"
                >
                    <i id="password-eye" class="fa-solid fa-eye"></i>
                </button>
            </div>
        </div>

        <!-- Confirmation -->
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                Confirmer le nouveau mot de passe
            </label>

            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                    <i class="fa-solid fa-lock text-gray-400"></i>
                </div>

                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Confirmez votre nouveau mot de passe"
                    class="w-full h-12 pl-11 pr-12 rounded-xl border border-gray-300 dark:border-gray-600
                           bg-gray-50 dark:bg-gray-700/70
                           text-gray-900 dark:text-white
                           placeholder-gray-400 dark:placeholder-gray-500
                           focus:bg-white dark:focus:bg-gray-700
                           focus:border-progBlue focus:ring-2 focus:ring-progBlue/20
                           transition-all duration-200 outline-none"
                >

                <button
                    type="button"
                    onclick="togglePassword('password_confirmation', 'confirmation-eye')"
                    class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
                    title="Afficher le mot de passe"
                >
                    <i id="confirmation-eye" class="fa-solid fa-eye"></i>
                </button>
            </div>
        </div>

        <!-- Bouton -->
        <button
            type="submit"
            class="w-full h-12 inline-flex items-center justify-center rounded-xl
                   bg-progBlue hover:bg-progDarkBlue
                   text-white font-semibold
                   shadow-md hover:shadow-lg
                   transition-all duration-200"
        >
            <i class="fa-solid fa-key mr-2"></i>
            Modifier mon mot de passe
        </button>

    </form>

</div>
```

</div>

<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

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
