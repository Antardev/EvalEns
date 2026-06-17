<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SuperAdmin') — ÉvalENS</title>
    <link rel="icon" type="image/png" href="{{ asset('dashboard/images/Evalensico.png') }}">
    <link href="{{ asset('dashboard/vendor/jqvmap/css/jqvmap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/css/style.css') }}" rel="stylesheet">
    <link href="https://cdn.lineicons.com/2.0/LineIcons.css" rel="stylesheet">
    @stack('styles')
</head>

<body>

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
            <a href="{{ route('superadmin.dashboard') }}" class="brand-logo">
                <img class="logo-abbr" src="{{ asset('dashboard/images/evalens-icon.svg') }}" alt="ÉvalENS" style="height:36px;">
                <img class="logo-compact" src="{{ asset('dashboard/evalens-logo.png') }}" alt="ÉvalENS" style="height:36px;">
                <img class="brand-title" src="{{ asset('dashboard/evalens-logo.png') }}" alt="ÉvalENS" style="height:36px;">
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
                            <li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link" href="{{ route('superadmin.inscriptions') }}" title="Demandes en attente">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    <span class="badge light text-white bg-primary rounded-pill ms-1">3</span>
                                </a>
                            </li>
                            <li class="nav-item dropdown header-profile">
                                <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown">
                                    <div class="header-info">
                                        <span>Bonjour, <strong>{{ auth()->user()->name ?? 'SuperAdmin' }}</strong></span>
                                        <small class="text-end fs-12 text-primary">Administrateur Système</small>
                                    </div>
                                    <img src="{{ asset('dashboard/images/profile/pic1.jpg') }}" width="20" alt="">
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
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

                    <li class="{{ request()->routeIs('superadmin.dashboard') ? 'mm-active' : '' }}">
                        <a href="{{ route('superadmin.dashboard') }}" class="ai-icon">
                            <i class="flaticon-381-networking"></i>
                            <span class="nav-text">Tableau de bord</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('superadmin.inscriptions*') ? 'mm-active' : '' }}">
                        <a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                            <i class="flaticon-381-user-9"></i>
                            <span class="nav-text">Inscriptions</span>
                            <span class="badge badge-xs badge-warning ms-2">3</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('superadmin.inscriptions') }}" class="{{ request()->routeIs('superadmin.inscriptions') ? 'mm-active' : '' }}">Demandes en attente</a></li>
                            <li><a href="{{ route('superadmin.inscriptions.historique') }}" class="{{ request()->routeIs('superadmin.inscriptions.historique') ? 'mm-active' : '' }}">Historique</a></li>
                        </ul>
                    </li>

                    <li class="{{ request()->routeIs('superadmin.universites*') ? 'mm-active' : '' }}">
                        <a href="{{ route('superadmin.universites') }}" class="ai-icon">
                            <i class="flaticon-381-layer-1"></i>
                            <span class="nav-text">Universités</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('superadmin.utilisateurs*') ? 'mm-active' : '' }}">
                        <a href="{{ route('superadmin.utilisateurs') }}" class="ai-icon">
                            <i class="flaticon-381-television"></i>
                            <span class="nav-text">Utilisateurs</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('superadmin.criteres*') ? 'mm-active' : '' }}">
                        <a href="{{ route('superadmin.criteres') }}" class="ai-icon">
                            <i class="flaticon-381-controls-3"></i>
                            <span class="nav-text">Critères d'évaluation</span>
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
                            <span class="nav-text">Rapports PDF</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('superadmin.logs*') ? 'mm-active' : '' }}">
                        <a href="{{ route('superadmin.logs') }}" class="ai-icon">
                            <i class="flaticon-381-network"></i>
                            <span class="nav-text">Logs d'audit</span>
                        </a>
                    </li>

                </ul>

                <div class="copyright">
                    <p><strong>ÉvalENS — SuperAdmin</strong> © {{ date('Y') }}</p>
                    <p>Plateforme d'évaluation universitaire</p>
                </div>
            </div>
        </div>

        {{-- Content Body --}}
        <div class="content-body">
            @yield('content')
        </div>

        <div class="footer">
            <div class="copyright">
                <p>Copyright © ÉvalENS {{ date('Y') }} — Espace SuperAdministrateur</p>
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
