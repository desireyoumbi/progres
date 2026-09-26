<!-- resources/views/member/dashboard.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Espace - PROGRÈS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        progBlue: '#0b5394',
                        progDarkBlue: '#073763',
                        progOrange: '#f1c232',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Topbar Membre -->
    <nav class="bg-gradient-to-r from-[#0b5394] to-[#073763] text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white p-1 rounded-full w-12 h-12 flex items-center justify-center shadow-md">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain rounded-full">
                </div>
                <div>
                    <span class="font-extrabold tracking-wider text-amber-300 uppercase block">PROGRÈS</span>
                    <span class="text-xs text-blue-200">Avancer ensemble. Devenir meilleurs.</span>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-amber-300">Membre actif</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-white/10 hover:bg-white/20 p-2.5 rounded-xl transition-all text-amber-300" title="Se déconnecter">
                        <i class="fa-solid fa-power-off text-lg"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Contenu Tableaux de bord Membre -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- Message de bienvenue -->
        <div class="bg-gradient-to-br from-[#0b5394] to-[#073763] rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden flex flex-col md:flex-row justify-between items-center">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-amber-400 opacity-10 rounded-full blur-2xl"></div>
            <div class="z-10 mb-4 md:mb-0">
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">Bienvenue sur votre espace, {{ auth()->user()->name }} !</h1>
                <p class="text-blue-100 text-sm max-w-xl">
                    Suivez l'évolution de vos cotisations de tontine rotative, vos versements en caisse collective et restez informé des prochaines réunions de la communauté.
                </p>
            </div>
            <div class="z-10 bg-white/10 border border-white/20 backdrop-blur-md px-6 py-4 rounded-2xl text-center">
                <i class="fa-solid fa-coins text-amber-300 text-3xl mb-1"></i>
                <p class="text-xs text-blue-200 uppercase font-semibold">Téléphone enregistré</p>
                <p class="text-lg font-bold text-white">{{ auth()->user()->phone }}</p>
            </div>
        </div>

        <!-- Grille des statistiques personnelles / Résumé -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 flex items-center space-x-4">
                <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-2xl font-bold shadow-inner">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase">Tontines Actives</p>
                    <p class="text-xl font-extrabold text-gray-800">{{ auth()->user()->tontines()->count() }}</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 flex items-center space-x-4">
                <div class="w-14 h-14 bg-blue-50 text-[#0b5394] rounded-2xl flex items-center justify-center text-2xl font-bold shadow-inner">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase">Contributions Effectuées</p>
                    <p class="text-xl font-extrabold text-gray-800">0 XAF</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 flex items-center space-x-4">
                <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl font-bold shadow-inner">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase">Prochaine Réunion</p>
                    <p class="text-sm font-bold text-gray-800 mt-1">À planifier</p>
                </div>
            </div>

        </div>

    </main>

</body>
</html>