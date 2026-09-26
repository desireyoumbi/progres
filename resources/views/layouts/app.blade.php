<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="fr" class="h-full">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'PROGRÈS - Gestion de Tontine')
    </title>


    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>


    <!-- FontAwesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >


    <!-- Configuration Tailwind -->
    <script>
        tailwind.config = {
            darkMode: 'class',

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


    <!--
        Script anti-flash :
        applique le thème avant l'affichage de la page.
    -->
    <script>

        if (
            localStorage.theme === 'dark' ||
            (
                !('theme' in localStorage) &&
                window.matchMedia(
                    '(prefers-color-scheme: dark)'
                ).matches
            )
        ) {

            document.documentElement.classList.add('dark');

        } else {

            document.documentElement.classList.remove('dark');

        }

    </script>

</head>


<body
    class="
        bg-gray-50
        dark:bg-gray-900
        text-gray-800
        dark:text-gray-100
        min-h-full
        flex
        flex-col
        transition-colors
        duration-200
    "
>


    <!-- =====================================================
         NAVIGATION GLOBALE
    ====================================================== -->

    <nav
        class="
            bg-[#0b5394]
            dark:bg-gray-900
            border-b
            border-blue-900/20
            dark:border-gray-800
            text-white
            shadow-md
            transition-colors
            duration-200
            sticky
            top-0
            z-50
        "
    >

        <div
            class="
                max-w-7xl
                mx-auto
                px-4
                sm:px-6
                lg:px-8
                h-16
                flex
                items-center
                justify-between
            "
        >


            <!-- =================================================
                 LOGO + MENU PRINCIPAL
            ================================================== -->

            <div class="flex items-center space-x-6">


                <!-- LOGO -->

                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center space-x-3 group"
                >

                    <div
                        class="
                            bg-white
                            p-1
                            rounded-full
                            w-9
                            h-9
                            flex
                            items-center
                            justify-center
                            shadow-md
                        "
                    >

                        <img
                            src="{{ asset('images/logo.png') }}"
                            alt="Logo"
                            class="
                                w-full
                                h-full
                                object-contain
                                rounded-full
                            "
                        >

                    </div>


                    <span
                        class="
                            font-bold
                            tracking-wider
                            text-amber-300
                            uppercase
                            group-hover:opacity-90
                            transition-opacity
                        "
                    >
                        PROGRÈS
                    </span>

                </a>


                <!-- =================================================
                     MENU DESKTOP
                ================================================== -->

                @auth

                    <div
                        class="
                            hidden
                            md:flex
                            items-center
                            space-x-1
                            text-sm
                            font-medium
                        "
                    >


                        <!-- TABLEAU DE BORD -->

                        <a
                            href="{{ route('dashboard') }}"
                            class="
                                px-3
                                py-2
                                rounded-xl
                                transition-all
                                hover:bg-white/10
                                dark:hover:bg-gray-800
                                {{ request()->routeIs('dashboard')
                                    ? 'bg-white/15 text-amber-300'
                                    : '' }}
                            "
                        >

                            <i
                                class="
                                    fa-solid
                                    fa-chart-pie
                                    mr-1.5
                                    text-amber-300
                                "
                            ></i>

                            Tableau de bord

                        </a>


                        <!-- =================================================
                             ADMINISTRATION
                             Président / Secrétaire / Trésorier
                        ================================================== -->

                        @if(
                            in_array(
                                auth()->user()->role,
                                [
                                    'president',
                                    'secretary',
                                    'treasurer'
                                ]
                            )
                        )


                            <!-- MEMBRES -->

                            <a
                                href="{{ route('admin.users.index') }}"
                                class="
                                    px-3
                                    py-2
                                    rounded-xl
                                    transition-all
                                    hover:bg-white/10
                                    dark:hover:bg-gray-800
                                    {{ request()->routeIs('admin.users.*')
                                        ? 'bg-white/15 text-amber-300'
                                        : '' }}
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-users
                                        mr-1.5
                                        text-amber-300
                                    "
                                ></i>

                                Membres

                            </a>


                            <!-- TONTINES -->

                            <a
                                href="{{ route('admin.tontines.index') }}"
                                class="
                                    px-3
                                    py-2
                                    rounded-xl
                                    transition-all
                                    hover:bg-white/10
                                    dark:hover:bg-gray-800
                                    {{ request()->routeIs('admin.tontines.*')
                                        ? 'bg-white/15 text-amber-300'
                                        : '' }}
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-coins
                                        mr-1.5
                                        text-amber-300
                                    "
                                ></i>

                                Tontines

                            </a>


                            <!-- CAISSE COLLECTIVE -->

                            <a
                                href="{{ route('admin.collective-contributions.index') }}"
                                class="
                                    px-3
                                    py-2
                                    rounded-xl
                                    transition-all
                                    hover:bg-white/10
                                    dark:hover:bg-gray-800
                                    {{ request()->routeIs('admin.collective-contributions.*')
                                        ? 'bg-white/15 text-amber-300'
                                        : '' }}
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-vault
                                        mr-1.5
                                        text-amber-300
                                    "
                                ></i>

                                Caisse Collective

                            </a>


                            <!-- CAISSE INDIVIDUELLE -->

                            <a
                                href="{{ route('admin.individual-contributions.index') }}"
                                class="
                                    px-3
                                    py-2
                                    rounded-xl
                                    transition-all
                                    hover:bg-white/10
                                    dark:hover:bg-gray-800
                                    {{ request()->routeIs('admin.individual-contributions.*')
                                        ? 'bg-white/15 text-amber-300'
                                        : '' }}
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-piggy-bank
                                        mr-1.5
                                        text-amber-300
                                    "
                                ></i>

                                Caisse Individuelle

                            </a>


                            <!-- CAISSE ROTATIVE -->

                            <a
                                href="{{ route('admin.rotating-contributions.index') }}"
                                class="
                                    px-3
                                    py-2
                                    rounded-xl
                                    transition-all
                                    hover:bg-white/10
                                    dark:hover:bg-gray-800
                                    {{ request()->routeIs('admin.rotating-contributions.*')
                                        ? 'bg-white/15 text-amber-300'
                                        : '' }}
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-rotate
                                        mr-1.5
                                        text-amber-300
                                    "
                                ></i>

                                Caisse Rotative

                            </a>


                            <!-- =================================================
                                 BÉNÉFICIAIRES
                            ================================================== -->

                            <a
                                href="{{ route('admin.beneficiaries.index') }}"
                                class="
                                    px-3
                                    py-2
                                    rounded-xl
                                    transition-all
                                    hover:bg-white/10
                                    dark:hover:bg-gray-800
                                    {{ request()->routeIs('admin.beneficiaries.*')
                                        ? 'bg-white/15 text-amber-300'
                                        : '' }}
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-user-check
                                        mr-1.5
                                        text-amber-300
                                    "
                                ></i>

                                Bénéficiaires

                            </a>


                            <!-- RAPPORTS -->

                            <a
                                href="{{ route('admin.reports.index') }}"
                                class="
                                    px-3
                                    py-2
                                    rounded-xl
                                    transition-all
                                    hover:bg-white/10
                                    dark:hover:bg-gray-800
                                    {{ request()->routeIs('admin.reports.*')
                                        ? 'bg-white/15 text-amber-300'
                                        : '' }}
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-file-lines
                                        mr-1.5
                                        text-amber-300
                                    "
                                ></i>

                                Rapports

                            </a>


                        <!-- =================================================
                             MENU MEMBRE
                        ================================================== -->

                        @elseif(auth()->user()->role === 'member')


                            <!-- MEMBRES -->

                            <a
                                href="{{ route('admin.users.index') }}"
                                class="
                                    px-3
                                    py-2
                                    rounded-xl
                                    transition-all
                                    hover:bg-white/10
                                    dark:hover:bg-gray-800
                                    {{ request()->routeIs('admin.users.*')
                                        ? 'bg-white/15 text-amber-300'
                                        : '' }}
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-users
                                        mr-1.5
                                        text-amber-300
                                    "
                                ></i>

                                Membres

                            </a>


                            <!-- CAISSE COLLECTIVE -->

                            <a
                                href="{{ route('admin.collective-contributions.index') }}"
                                class="
                                    px-3
                                    py-2
                                    rounded-xl
                                    transition-all
                                    hover:bg-white/10
                                    dark:hover:bg-gray-800
                                    {{ request()->routeIs('admin.collective-contributions.*')
                                        ? 'bg-white/15 text-amber-300'
                                        : '' }}
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-vault
                                        mr-1.5
                                        text-amber-300
                                    "
                                ></i>

                                Caisse Collective

                            </a>


                            <!-- CAISSE INDIVIDUELLE -->

                            <a
                                href="{{ route('admin.individual-contributions.index') }}"
                                class="
                                    px-3
                                    py-2
                                    rounded-xl
                                    transition-all
                                    hover:bg-white/10
                                    dark:hover:bg-gray-800
                                    {{ request()->routeIs('admin.individual-contributions.*')
                                        ? 'bg-white/15 text-amber-300'
                                        : '' }}
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-piggy-bank
                                        mr-1.5
                                        text-amber-300
                                    "
                                ></i>

                                Caisse Individuelle

                            </a>


                            <!-- CAISSE ROTATIVE -->

                            <a
                                href="{{ route('admin.rotating-contributions.index') }}"
                                class="
                                    px-3
                                    py-2
                                    rounded-xl
                                    transition-all
                                    hover:bg-white/10
                                    dark:hover:bg-gray-800
                                    {{ request()->routeIs('admin.rotating-contributions.*')
                                        ? 'bg-white/15 text-amber-300'
                                        : '' }}
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-rotate
                                        mr-1.5
                                        text-amber-300
                                    "
                                ></i>

                                Caisse Rotative

                            </a>


                            <!-- BÉNÉFICIAIRES -->

                            <a
                                href="{{ route('admin.beneficiaries.index') }}"
                                class="
                                    px-3
                                    py-2
                                    rounded-xl
                                    transition-all
                                    hover:bg-white/10
                                    dark:hover:bg-gray-800
                                    {{ request()->routeIs('admin.beneficiaries.*')
                                        ? 'bg-white/15 text-amber-300'
                                        : '' }}
                                "
                            >

                                <i
                                    class="
                                        fa-solid
                                        fa-user-check
                                        mr-1.5
                                        text-amber-300
                                    "
                                ></i>

                                Bénéficiaires

                            </a>

                        @endif

                    </div>

                @endauth

            </div>


            <!-- =================================================
                 CONTRÔLES DROITE
            ================================================== -->

            <div class="flex items-center space-x-3">


                <!-- MODE SOMBRE -->

                <button
                    id="themeToggle"
                    type="button"
                    class="
                        p-2.5
                        rounded-xl
                        bg-white/10
                        dark:bg-gray-800
                        text-amber-300
                        hover:bg-white/20
                        dark:hover:bg-gray-700
                        transition-all
                        focus:outline-none
                    "
                    title="Changer de thème"
                >

                    <i
                        class="
                            fa-solid
                            fa-moon
                            dark:hidden
                            text-lg
                        "
                    ></i>

                    <i
                        class="
                            fa-solid
                            fa-sun
                            hidden
                            dark:inline
                            text-lg
                            text-amber-400
                        "
                    ></i>

                </button>


                @auth

                    <!-- DÉCONNEXION -->

                    <form
                        action="{{ route('logout') }}"
                        method="POST"
                        class="inline-block"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="
                                text-sm
                                bg-white/10
                                dark:bg-gray-800
                                hover:bg-white/20
                                dark:hover:bg-gray-700
                                px-4
                                py-2
                                rounded-xl
                                transition-all
                                flex
                                items-center
                                space-x-2
                                text-white
                            "
                        >

                            <i
                                class="
                                    fa-solid
                                    fa-power-off
                                    text-amber-300
                                "
                            ></i>

                            <span class="hidden sm:inline">
                                Déconnexion
                            </span>

                        </button>

                    </form>

                @endauth

            </div>

        </div>


        <!-- =====================================================
             MENU MOBILE
        ====================================================== -->

        @auth

            <div
                class="
                    md:hidden
                    flex
                    items-center
                    justify-around
                    bg-[#073763]
                    dark:bg-gray-900/90
                    border-t
                    border-white/10
                    px-2
                    py-2
                    text-xs
                    overflow-x-auto
                "
            >


                <!-- ACCUEIL -->

                <a
                    href="{{ route('dashboard') }}"
                    class="
                        px-3
                        py-1.5
                        rounded-lg
                        whitespace-nowrap
                        {{ request()->routeIs('dashboard')
                            ? 'bg-white/20 text-amber-300 font-bold'
                            : 'text-gray-200' }}
                    "
                >

                    <i class="fa-solid fa-chart-pie mr-1"></i>

                    Accueil

                </a>


                <!-- =================================================
                     MOBILE :
                     ADMIN
                ================================================== -->

                @if(
                    in_array(
                        auth()->user()->role,
                        [
                            'president',
                            'secretary',
                            'treasurer'
                        ]
                    )
                )


                    <!-- MEMBRES -->

                    <a
                        href="{{ route('admin.users.index') }}"
                        class="
                            px-3
                            py-1.5
                            rounded-lg
                            whitespace-nowrap
                            {{ request()->routeIs('admin.users.*')
                                ? 'bg-white/20 text-amber-300 font-bold'
                                : 'text-gray-200' }}
                        "
                    >

                        <i class="fa-solid fa-users mr-1"></i>

                        Membres

                    </a>


                    <!-- TONTINES -->

                    <a
                        href="{{ route('admin.tontines.index') }}"
                        class="
                            px-3
                            py-1.5
                            rounded-lg
                            whitespace-nowrap
                            {{ request()->routeIs('admin.tontines.*')
                                ? 'bg-white/20 text-amber-300 font-bold'
                                : 'text-gray-200' }}
                        "
                    >

                        <i class="fa-solid fa-coins mr-1"></i>

                        Tontines

                    </a>


                    <!-- COLLECTIVE -->

                    <a
                        href="{{ route('admin.collective-contributions.index') }}"
                        class="
                            px-3
                            py-1.5
                            rounded-lg
                            whitespace-nowrap
                            {{ request()->routeIs('admin.collective-contributions.*')
                                ? 'bg-white/20 text-amber-300 font-bold'
                                : 'text-gray-200' }}
                        "
                    >

                        <i class="fa-solid fa-vault mr-1"></i>

                        Collective

                    </a>


                    <!-- INDIVIDUELLE -->

                    <a
                        href="{{ route('admin.individual-contributions.index') }}"
                        class="
                            px-3
                            py-1.5
                            rounded-lg
                            whitespace-nowrap
                            {{ request()->routeIs('admin.individual-contributions.*')
                                ? 'bg-white/20 text-amber-300 font-bold'
                                : 'text-gray-200' }}
                        "
                    >

                        <i class="fa-solid fa-piggy-bank mr-1"></i>

                        Individuelle

                    </a>


                    <!-- ROTATIVE -->

                    <a
                        href="{{ route('admin.rotating-contributions.index') }}"
                        class="
                            px-3
                            py-1.5
                            rounded-lg
                            whitespace-nowrap
                            {{ request()->routeIs('admin.rotating-contributions.*')
                                ? 'bg-white/20 text-amber-300 font-bold'
                                : 'text-gray-200' }}
                        "
                    >

                        <i class="fa-solid fa-rotate mr-1"></i>

                        Rotative

                    </a>


                    <!-- BÉNÉFICIAIRES -->

                    <a
                        href="{{ route('admin.beneficiaries.index') }}"
                        class="
                            px-3
                            py-1.5
                            rounded-lg
                            whitespace-nowrap
                            {{ request()->routeIs('admin.beneficiaries.*')
                                ? 'bg-white/20 text-amber-300 font-bold'
                                : 'text-gray-200' }}
                        "
                    >

                        <i class="fa-solid fa-user-check mr-1"></i>

                        Bénéficiaires

                    </a>


                    <!-- RAPPORTS -->

                    <a
                        href="{{ route('admin.reports.index') }}"
                        class="
                            px-3
                            py-1.5
                            rounded-lg
                            whitespace-nowrap
                            {{ request()->routeIs('admin.reports.*')
                                ? 'bg-white/20 text-amber-300 font-bold'
                                : 'text-gray-200' }}
                        "
                    >

                        <i class="fa-solid fa-file-lines mr-1"></i>

                        Rapports

                    </a>


                <!-- =================================================
                     MOBILE :
                     MEMBRE SIMPLE
                ================================================== -->

                @elseif(auth()->user()->role === 'member')


                    <!-- MEMBRES -->

                    <a
                        href="{{ route('admin.users.index') }}"
                        class="
                            px-3
                            py-1.5
                            rounded-lg
                            whitespace-nowrap
                            {{ request()->routeIs('admin.users.*')
                                ? 'bg-white/20 text-amber-300 font-bold'
                                : 'text-gray-200' }}
                        "
                    >

                        <i class="fa-solid fa-users mr-1"></i>

                        Membres

                    </a>


                    <!-- COLLECTIVE -->

                    <a
                        href="{{ route('admin.collective-contributions.index') }}"
                        class="
                            px-3
                            py-1.5
                            rounded-lg
                            whitespace-nowrap
                            {{ request()->routeIs('admin.collective-contributions.*')
                                ? 'bg-white/20 text-amber-300 font-bold'
                                : 'text-gray-200' }}
                        "
                    >

                        <i class="fa-solid fa-vault mr-1"></i>

                        Collective

                    </a>


                    <!-- INDIVIDUELLE -->

                    <a
                        href="{{ route('admin.individual-contributions.index') }}"
                        class="
                            px-3
                            py-1.5
                            rounded-lg
                            whitespace-nowrap
                            {{ request()->routeIs('admin.individual-contributions.*')
                                ? 'bg-white/20 text-amber-300 font-bold'
                                : 'text-gray-200' }}
                        "
                    >

                        <i class="fa-solid fa-piggy-bank mr-1"></i>

                        Individuelle

                    </a>


                    <!-- ROTATIVE -->

                    <a
                        href="{{ route('admin.rotating-contributions.index') }}"
                        class="
                            px-3
                            py-1.5
                            rounded-lg
                            whitespace-nowrap
                            {{ request()->routeIs('admin.rotating-contributions.*')
                                ? 'bg-white/20 text-amber-300 font-bold'
                                : 'text-gray-200' }}
                        "
                    >

                        <i class="fa-solid fa-rotate mr-1"></i>

                        Rotative

                    </a>


                    <!-- BÉNÉFICIAIRES -->

                    <a
                        href="{{ route('admin.beneficiaries.index') }}"
                        class="
                            px-3
                            py-1.5
                            rounded-lg
                            whitespace-nowrap
                            {{ request()->routeIs('admin.beneficiaries.*')
                                ? 'bg-white/20 text-amber-300 font-bold'
                                : 'text-gray-200' }}
                        "
                    >

                        <i class="fa-solid fa-user-check mr-1"></i>

                        Bénéficiaires

                    </a>

                @endif

            </div>

        @endauth

    </nav>


    <!-- =====================================================
         CONTENU PRINCIPAL
    ====================================================== -->

    <main
        class="
            flex-grow
            max-w-7xl
            w-full
            mx-auto
            px-4
            sm:px-6
            lg:px-8
            py-8
        "
    >

        @yield('content')

    </main>


    <!-- =====================================================
         PIED DE PAGE
    ====================================================== -->

    <footer
        class="
            bg-white
            dark:bg-gray-900
            border-t
            border-gray-200
            dark:border-gray-800
            py-4
            text-center
            text-xs
            text-gray-500
            dark:text-gray-400
            transition-colors
            duration-200
        "
    >

        <p>
            &copy; {{ date('Y') }}
            PROGRÈS — Avancer ensemble. Devenir meilleurs.
            Tous droits réservés.
        </p>

    </footer>


    <!-- =====================================================
         GESTION DU MODE SOMBRE
    ====================================================== -->

    <script>

        const themeToggleBtn =
            document.getElementById('themeToggle');


        if (themeToggleBtn) {

            themeToggleBtn.addEventListener(
                'click',
                function () {

                    if (
                        document.documentElement.classList.contains(
                            'dark'
                        )
                    ) {

                        document.documentElement.classList.remove(
                            'dark'
                        );

                        localStorage.theme = 'light';

                    } else {

                        document.documentElement.classList.add(
                            'dark'
                        );

                        localStorage.theme = 'dark';

                    }

                }
            );

        }

    </script>

</body>

</html>