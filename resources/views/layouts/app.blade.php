<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ÉvalENS') — ÉvalENS</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('dashboard/images/favicon.png') }}">
    <link href="{{ asset('dashboard/vendor/jqvmap/css/jqvmap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/css/style.css') }}" rel="stylesheet">
    <link href="https://cdn.lineicons.com/2.0/LineIcons.css" rel="stylesheet">
    @stack('styles')
</head>

<body>

    @php
        $isAdminUniv    = request()->routeIs('adminuniversity.*');
        $isTeacher      = request()->routeIs('teacher.*');
        $isGestionnaire = request()->routeIs('gestionnaire.*');
        $isSuperAdmin   = request()->routeIs('superadmin.*');

        // Sur la page profil, revenir au sidebar du rôle de l'utilisateur
        if (request()->routeIs('profile.*') && auth()->check()) {
            $role = auth()->user()->role;
            $isAdminUniv    = $role === 'directeur';
            $isTeacher      = $role === 'enseignant';
            $isGestionnaire = $role === 'gestionnaire';
            $isSuperAdmin   = $role === 'superadmin';
        }

        $homeRoute = match(true) {
            $isAdminUniv    => route('adminuniversity.dashboard'),
            $isTeacher      => route('teacher.dashboard'),
            $isGestionnaire => route('gestionnaire.dashboard'),
            $isSuperAdmin   => route('superadmin.dashboard'),
            default         => route('home'),
        };

        $roleLabel = match(true) {
            $isAdminUniv    => 'Admin Université',
            $isTeacher      => 'Enseignant',
            $isGestionnaire => 'Gestionnaire',
            $isSuperAdmin   => 'Super Admin',
            default         => 'Utilisateur',
        };
    @endphp

    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>

    <div id="main-wrapper">

        {{-- Nav Header --}}
        <div class="nav-header">
            <a href="{{ $homeRoute }}" class="brand-logo">
                <img class="logo-abbr" src="{{ asset('dashboard/images/evalens-icon.svg') }}" alt="ÉvalENS" style="height:36px;">
                <img class="logo-compact" src="{{ asset('dashboard/images/evalens-logo.svg') }}" alt="ÉvalENS" style="height:36px;">
                <img class="brand-title" src="{{ asset('dashboard/images/evalens-logo.svg') }}" alt="ÉvalENS" style="height:36px;">
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>

        {{-- Top Bar --}}
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <div class="input-group search-area">
                                <input type="text" class="form-control" placeholder="Rechercher...">
                                <span class="input-group-text">
                                    <a href="javascript:void(0)"><i class="flaticon-381-search-2"></i></a>
                                </span>
                            </div>
                        </div>
                        <ul class="navbar-nav header-right">
                            <li class="nav-item dropdown header-profile">
                                <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown">
                                    <div class="header-info">
                                        <span>{{ auth()->user()->name ?? 'Utilisateur' }}</span>
                                        <small class="text-end fs-12 text-primary">{{ $roleLabel }}</small>
                                    </div>
                                    <img src="{{ asset('dashboard/images/profile/pic1.jpg') }}" width="20" alt="">
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="{{ route('profile.show') }}" class="dropdown-item ai-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                        <span class="ms-2">Mon profil</span>
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item ai-icon border-0 bg-transparent w-100 text-start">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                                <polyline points="16 17 21 12 16 7"></polyline>
                                                <line x1="21" y1="12" x2="9" y2="12"></line>
                                            </svg>
                                            <span class="ms-2">Déconnexion</span>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="deznav">
            <div class="deznav-scroll">
                <ul class="metismenu" id="menu">

                    @if($isAdminUniv)

                        <li class="{{ request()->routeIs('adminuniversity.dashboard') ? 'mm-active' : '' }}">
                            <a href="{{ route('adminuniversity.dashboard') }}" class="ai-icon">
                                <i class="flaticon-381-networking"></i>
                                <span class="nav-text">Tableau de bord</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('adminuniversity.etudiants*') ? 'mm-active' : '' }}">
                            <a href="{{ route('adminuniversity.etudiants') }}" class="ai-icon">
                                <i class="flaticon-381-user-9"></i>
                                <span class="nav-text">Étudiants</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('adminuniversity.enseignants*') ? 'mm-active' : '' }}">
                            <a href="{{ route('adminuniversity.enseignants') }}" class="ai-icon">
                                <i class="flaticon-381-user-9"></i>
                                <span class="nav-text">Enseignants</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('adminuniversity.annexes*') ? 'mm-active' : '' }}">
                            <a href="{{ route('adminuniversity.annexes') }}" class="ai-icon">
                                <i class="flaticon-381-map-2"></i>
                                <span class="nav-text">Annexes</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('adminuniversity.periodes*') ? 'mm-active' : '' }}">
                            <a href="{{ route('adminuniversity.periodes') }}" class="ai-icon">
                                <i class="flaticon-381-controls-3"></i>
                                <span class="nav-text">Périodes d'évaluation</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('adminuniversity.formations*') ? 'mm-active' : '' }}">
                            <a href="{{ route('adminuniversity.formations') }}" class="ai-icon">
                                <i class="flaticon-381-layer-1"></i>
                                <span class="nav-text">Formations &amp; UE</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('adminuniversity.questionnaires*') ? 'mm-active' : '' }}">
                            <a href="{{ route('adminuniversity.questionnaires') }}" class="ai-icon">
                                <i class="flaticon-381-notepad"></i>
                                <span class="nav-text">Questionnaires</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('adminuniversity.rapports*') ? 'mm-active' : '' }}">
                            <a href="{{ route('adminuniversity.rapports') }}" class="ai-icon">
                                <i class="flaticon-381-internet"></i>
                                <span class="nav-text">Rapports &amp; Exports</span>
                            </a>
                        </li>

                    @elseif($isTeacher)

                        <li class="{{ request()->routeIs('teacher.dashboard') ? 'mm-active' : '' }}">
                            <a href="{{ route('teacher.dashboard') }}" class="ai-icon">
                                <i class="flaticon-381-networking"></i>
                                <span class="nav-text">Tableau de bord</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('teacher.resultats*') ? 'mm-active' : '' }}">
                            <a href="{{ route('teacher.resultats') }}" class="ai-icon">
                                <i class="flaticon-381-controls-3"></i>
                                <span class="nav-text">Mes résultats</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('teacher.evolution*') ? 'mm-active' : '' }}">
                            <a href="{{ route('teacher.evolution') }}" class="ai-icon">
                                <i class="flaticon-381-internet"></i>
                                <span class="nav-text">Évolution</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('teacher.commentaires*') ? 'mm-active' : '' }}">
                            <a href="{{ route('teacher.commentaires') }}" class="ai-icon">
                                <i class="flaticon-381-notepad"></i>
                                <span class="nav-text">Commentaires</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('teacher.rapport*') ? 'mm-active' : '' }}">
                            <a href="{{ route('teacher.rapport') }}" class="ai-icon">
                                <i class="flaticon-381-layer-1"></i>
                                <span class="nav-text">Mon rapport PDF</span>
                            </a>
                        </li>

                    @elseif($isGestionnaire)

                        <li class="{{ request()->routeIs('gestionnaire.dashboard') ? 'mm-active' : '' }}">
                            <a href="{{ route('gestionnaire.dashboard') }}" class="ai-icon">
                                <i class="flaticon-381-networking"></i>
                                <span class="nav-text">Tableau de bord</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('gestionnaire.etudiants') ? 'mm-active' : '' }}">
                            <a href="{{ route('gestionnaire.etudiants') }}" class="ai-icon">
                                <i class="flaticon-381-user-9"></i>
                                <span class="nav-text">Étudiants</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('gestionnaire.enseignants') ? 'mm-active' : '' }}">
                            <a href="{{ route('gestionnaire.enseignants') }}" class="ai-icon">
                                <i class="flaticon-381-user-9"></i>
                                <span class="nav-text">Enseignants</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('gestionnaire.emplois-du-temps*') ? 'mm-active' : '' }}">
                            <a href="{{ route('gestionnaire.emplois-du-temps') }}" class="ai-icon">
                                <i class="flaticon-381-controls-3"></i>
                                <span class="nav-text">Emplois du temps</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('gestionnaire.liens*') ? 'mm-active' : '' }}">
                            <a href="{{ route('gestionnaire.liens') }}" class="ai-icon">
                                <i class="flaticon-381-notepad"></i>
                                <span class="nav-text">Questionnaires</span>
                            </a>
                        </li>

                    @elseif($isSuperAdmin)

                        <li class="{{ request()->routeIs('superadmin.dashboard') ? 'mm-active' : '' }}">
                            <a href="{{ route('superadmin.dashboard') }}" class="ai-icon">
                                <i class="flaticon-381-networking"></i>
                                <span class="nav-text">Tableau de bord</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('superadmin.inscriptions*') ? 'mm-active' : '' }}">
                            <a href="{{ route('superadmin.inscriptions') }}" class="ai-icon">
                                <i class="flaticon-381-user-9"></i>
                                <span class="nav-text">Inscriptions</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('superadmin.universites*') ? 'mm-active' : '' }}">
                            <a href="{{ route('superadmin.universites') }}" class="ai-icon">
                                <i class="flaticon-381-layer-1"></i>
                                <span class="nav-text">Universités</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('superadmin.utilisateurs*') ? 'mm-active' : '' }}">
                            <a href="{{ route('superadmin.utilisateurs') }}" class="ai-icon">
                                <i class="flaticon-381-user-9"></i>
                                <span class="nav-text">Utilisateurs</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('superadmin.statistiques*') ? 'mm-active' : '' }}">
                            <a href="{{ route('superadmin.statistiques') }}" class="ai-icon">
                                <i class="flaticon-381-internet"></i>
                                <span class="nav-text">Statistiques</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('superadmin.rapports*') ? 'mm-active' : '' }}">
                            <a href="{{ route('superadmin.rapports') }}" class="ai-icon">
                                <i class="flaticon-381-notepad"></i>
                                <span class="nav-text">Rapports</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('superadmin.logs*') ? 'mm-active' : '' }}">
                            <a href="{{ route('superadmin.logs') }}" class="ai-icon">
                                <i class="flaticon-381-controls-3"></i>
                                <span class="nav-text">Logs</span>
                            </a>
                        </li>

                    @endif

                </ul>

                <div class="copyright">
                    <p><strong>ÉvalENS</strong> © {{ date('Y') }}</p>
                    <p>{{ $roleLabel }}</p>
                </div>
            </div>
        </div>

        {{-- Content Body --}}
        <div class="content-body">
            @yield('content')
        </div>

        <div class="footer">
            <div class="copyright">
                <p>Copyright © ÉvalENS {{ date('Y') }}</p>
            </div>
        </div>

    </div>{{-- /#main-wrapper --}}

    <script src="{{ asset('dashboard/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('dashboard/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/custom.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/deznav-init.js') }}"></script>
    @stack('scripts')

</body>
</html>
