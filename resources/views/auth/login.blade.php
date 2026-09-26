<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - PROGRÈS</title>
    <!-- Tailwind CSS pour un design propre et ultra-responsive -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome pour les icônes de pièces / utilisateur / cadenas / yeux -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        progBlue: '#0b5394', // Bleu principal du logo
                        progDarkBlue: '#073763',
                        progOrange: '#f1c232', // Orange/Or du logo
                        progLightOrange: '#f9cb9c',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-slate-100 to-amber-50 min-h-screen flex items-center justify-center p-4">

    <!-- Conteneur Principal de la Carte -->
    <div class="bg-w-full max-w-4xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-2 relative border border-gray-100">

        <!-- Colonne Gauche : Panneau d'Information et Visuel (Couleurs du Logo) -->
        <div class="bg-gradient-to-br from-[#0b5394] to-[#073763] text-white p-8 md:p-12 flex flex-col justify-between relative overflow-hidden">
            <!-- Cercles décoratifs d'arrière-plan (rappelant les pièces / bulles) -->
            <div class="absolute -top-12 -left-12 w-40 h-40 bg-amber-400 opacity-20 rounded-full blur-xl"></div>
            <div class="absolute bottom-[-20px] right-[-20px] w-48 h-48 bg-blue-400 opacity-20 rounded-full blur-xl"></div>

            <!-- En-tête : Logo et Nom -->
            <div class="flex items-center space-x-3 z-10">
                <div class="bg-white p-1.5 rounded-full shadow-md flex items-center justify-center w-12 h-12">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo PROGRÈS" class="w-full h-full object-contain rounded-full">
                </div>
                <span class="text-xl font-extrabold tracking-wider uppercase text-amber-300">PROGRÈS</span>
            </div>

            <!-- Message Central orienté Tontine & Réunion -->
            <div class="my-auto py-8 z-10">
                <div class="inline-block p-3 bg-white/10 rounded-2xl mb-4 backdrop-blur-sm border border-white/10">
                    <i class="fa-solid fa-coins text-amber-300 text-3xl"></i>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold mb-3 leading-tight">Espace Membres</h1>
                <p class="text-blue-100 text-sm md:text-base leading-relaxed mb-6">
                    Connectez-vous pour suivre vos cotisations, participer aux réunions en ligne et faire prospérer notre communauté.
                </p>
                <div class="flex items-center space-x-2 text-xs text-amber-200 font-semibold uppercase tracking-wider bg-black/20 py-2 px-3.5 rounded-xl w-fit">
                    <i class="fa-solid fa-handshake"></i>
                    <span>Jeunesse • Échanges • Actions • Avenir</span>
                </div>
            </div>

            <!-- Bas de panneau : Citation -->
            <div class="text-xs text-blue-200 z-10 italic border-t border-white/10 pt-4">
                "Avancer ensemble. Devenir meilleurs."
            </div>
        </div>

        <!-- Colonne Droite : Formulaire de Connexion Sécurisé (Sans Inscription) -->
        <div class="p-8 md:p-12 flex flex-col justify-center bg-white">
            
            <div class="mb-8">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 tracking-tight">Bon retour !</h2>
                <p class="text-sm text-gray-500 mt-1">Veuillez entrer vos identifiants pour accéder à votre session.</p>
            </div>

            <!-- Affichage des erreurs globales / identifiants invalides -->
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-r-lg">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Formulaire pointant vers la route de connexion Laravel -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Champ Numéro de Téléphone (Identifiant principal) -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Numéro de Téléphone</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-phone"></i>
                        </span>
                        <input type="text" name="phone" value="{{ old('phone') }}" required autofocus
                            placeholder="Ex: 690000000"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-[#0b5394] focus:bg-white transition-all">
                    </div>
                </div>

                <!-- Champ Mot de Passe avec bouton "œil" interactif -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Mot de passe</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" required
                            placeholder="••••••••"
                            class="w-full pl-10 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-[#0b5394] focus:bg-white transition-all">
                        
                        <!-- Bouton de bascule de visibilité -->
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                            <i class="fa-solid fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Option Se souvenir de moi -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-[#0b5394] border-gray-300 rounded focus:ring-[#0b5394]">
                        <span class="ml-2 text-gray-600 text-xs font-medium">Se souvenir de moi</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs text-[#0b5394] hover:underline font-semibold">Mot de passe oublié ?</a>
                    @endif
                </div>

                <!-- Bouton de Connexion aux couleurs du logo -->
                <button type="submit" 
                    class="w-full py-3.5 px-4 bg-gradient-to-r from-[#0b5394] to-[#073763] hover:from-[#073763] hover:to-[#0b5394] text-white font-bold rounded-xl shadow-lg shadow-blue-900/20 transition-all duration-200 transform hover:-translate-y-0.5 flex items-center justify-center space-x-2 text-sm">
                    <span>SE CONNECTER</span>
                    <i class="fa-solid fa-arrow-right-to-bracket text-amber-300"></i>
                </button>
            </form>

            <!-- Note informative de bas de page -->
            <div class="mt-8 text-center border-t border-gray-100 pt-4">
                <p class="text-xs text-gray-400">
                    Accès restreint aux membres inscrits de l’association.<br>
                    Contactez le bureau exécutif en cas de problème d'accès.
                </p>
            </div>

        </div>

    </div>

    <!-- Script JavaScript pour basculer l'affichage du mot de passe -->
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function () {
            // Bascule le type de l'input
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Change l'icône de l'œil (ouvert/fermé)
            if (type === 'text') {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>