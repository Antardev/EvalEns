@extends('layouts.app')

@section('title', 'Tableau de bord — ' . $annexe->nom)

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">{{ $annexe->nom }}</h2>
            <p class="mb-0">{{ $annexe->university->nom ?? '' }}</p>
        </div>
        @if($annexe->ville)
            <span class="badge badge-primary px-3 py-2 fs-13">
                <i class="lni lni-map-marker me-1"></i>
                {{ $annexe->ville }}{{ $annexe->pays ? ', '.$annexe->pays : '' }}
            </span>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="row">
        <div class="col-xl-4 col-md-6 col-sm-6">
            <a href="{{ route('gestionnaire.enseignants') }}" class="widget-stat card text-decoration-none">
                <div class="card-body p-4">
                    <div class="media ai-icon d-flex">
                        <span class="me-3 bgl-success text-success">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#2BC155" stroke-width="2">
                                <rect x="2" y="3" width="20" height="14" rx="2"/>
                                <line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
                            </svg>
                        </span>
                        <div class="media-body">
                            <h3 class="mb-0 text-black"><span class="counter ms-0">{{ $stats['enseignants'] }}</span></h3>
                            <p class="mb-0">Enseignants</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-md-6 col-sm-6">
            <a href="{{ route('gestionnaire.liens') }}" class="widget-stat card text-decoration-none">
                <div class="card-body p-4">
                    <div class="media ai-icon d-flex">
                        <span class="me-3 bgl-warning text-warning">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#FFAB2D" stroke-width="2">
                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                            </svg>
                        </span>
                        <div class="media-body">
                            <h3 class="mb-0 text-black"><span class="counter ms-0">{{ $stats['liens'] }}</span></h3>
                            <p class="mb-0">Liens questionnaires</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6 col-sm-6">
            <a href="{{ route('gestionnaire.liens') }}" class="widget-stat card text-decoration-none">
                <div class="card-body p-4">
                    <div class="media ai-icon d-flex">
                        <span class="me-3 bgl-danger text-danger">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#F94687" stroke-width="2">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                            </svg>
                        </span>
                        <div class="media-body">
                            <h3 class="mb-0 text-black"><span class="counter ms-0">{{ $stats['reponses'] }}</span></h3>
                            <p class="mb-0">Réponses reçues</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Infos de l'annexe --}}
        <div class="col-xl-5 col-lg-12">
            <div class="card h-100">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-0">Informations de l'annexe</h4>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex gap-3 py-2 border-bottom">
                            <span class="text-muted" style="min-width:110px;">Établissement</span>
                            <span class="font-w500">{{ $annexe->university->nom ?? '—' }}</span>
                        </li>
                        <li class="d-flex gap-3 py-2 border-bottom">
                            <span class="text-muted" style="min-width:110px;">Site</span>
                            <span class="font-w500">{{ $annexe->nom }}</span>
                        </li>
                        @if($annexe->adresse)
                        <li class="d-flex gap-3 py-2 border-bottom">
                            <span class="text-muted" style="min-width:110px;">Adresse</span>
                            <span>{{ $annexe->adresse }}</span>
                        </li>
                        @endif
                        @if($annexe->ville || $annexe->pays)
                        <li class="d-flex gap-3 py-2 border-bottom">
                            <span class="text-muted" style="min-width:110px;">Localisation</span>
                            <span>{{ implode(', ', array_filter([$annexe->ville, $annexe->pays])) }}</span>
                        </li>
                        @endif
                        @if($annexe->email)
                        <li class="d-flex gap-3 py-2 border-bottom">
                            <span class="text-muted" style="min-width:110px;">Email</span>
                            <span>{{ $annexe->email }}</span>
                        </li>
                        @endif
                        @if($annexe->telephone)
                        <li class="d-flex gap-3 py-2">
                            <span class="text-muted" style="min-width:110px;">Téléphone</span>
                            <span>{{ $annexe->telephone }}</span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        {{-- Enseignants récents --}}
        <div class="col-xl-7 col-lg-12">
            <div class="card h-100">
                <div class="card-header border-0 pb-0 d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Enseignants récents</h4>
                    <a href="{{ route('gestionnaire.enseignants') }}" class="btn btn-xs btn-outline-success">Voir tout</a>
                </div>
                <div class="card-body p-0">
                    @php
                        $recents = \App\Models\User::where('annexe_id', $annexe->id)
                            ->where('role', 'enseignant')
                            ->latest()->limit(6)->get();
                    @endphp

                    @if($recents->isEmpty())
                        <div class="text-center py-4 text-muted fs-13">
                            <i class="lni lni-users d-block fs-2 mb-2"></i>
                            Aucun enseignant inscrit dans cette annexe.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Membre</th>
                                        <th>Email</th>
                                        <th>Rôle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recents as $m)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="d-flex align-items-center justify-content-center rounded-circle text-white fs-12 fw-bold"
                                                    style="width:32px;height:32px;flex-shrink:0;background:#2BC155;">
                                                    {{ strtoupper(substr($m->prenom, 0, 1)) }}
                                                </div>
                                                <span class="font-w500">{{ $m->prenom }} {{ $m->nom }}</span>
                                            </div>
                                        </td>
                                        <td class="text-muted fs-13">{{ $m->email }}</td>
                                        <td>
                                            <span class="badge badge-sm badge-success">Enseignant</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('dashboard/vendor/waypoints/jquery.waypoints.min.js') }}"></script>
<script src="{{ asset('dashboard/vendor/jquery.counterup/jquery.counterup.min.js') }}"></script>
<script>jQuery('.counter').counterUp({ delay: 10, time: 1000 });</script>
@endpush
