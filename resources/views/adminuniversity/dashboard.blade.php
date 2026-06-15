@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Tableau de bord</h2>
            <p class="mb-0">Université Paris-Saclay — Vue d'ensemble</p>
        </div>
        <span class="badge badge-success px-3 py-2 fs-13">Période active : S2 2025-2026</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="row">
        <div class="col-xl-3 col-md-6 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon d-flex">
                        <span class="me-3 bgl-primary text-primary">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#2F4CDD" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </span>
                        <div class="media-body">
                            <h3 class="mb-0 text-black"><span class="counter ms-0">87</span></h3>
                            <p class="mb-0">Étudiants</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon d-flex">
                        <span class="me-3 bgl-success text-success">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#2BC155" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        </span>
                        <div class="media-body">
                            <h3 class="mb-0 text-black"><span class="counter ms-0">24</span></h3>
                            <p class="mb-0">Enseignants</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon d-flex">
                        <span class="me-3 bgl-warning text-warning">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#FFAB2D" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        </span>
                        <div class="media-body">
                            <h3 class="mb-0 text-black"><span class="counter ms-0">412</span></h3>
                            <p class="mb-0">Évaluations soumises</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon d-flex">
                        <span class="me-3 bgl-danger text-danger">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#FF2E2E" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                        </span>
                        <div class="media-body">
                            <h3 class="mb-0 text-black"><span class="counter ms-0">85</span>%</h3>
                            <p class="mb-0">Taux de participation</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Chart évaluations par formation --}}
        <div class="col-xl-8 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Évaluations par formation</h4>
                    <small class="text-muted">Nombre d'évaluations reçues par UE ce semestre</small>
                </div>
                <div class="card-body">
                    <canvas id="chartFormations" height="100"></canvas>
                </div>
            </div>
        </div>

        {{-- Périodes actives --}}
        <div class="col-xl-4 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0 d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Périodes actives</h4>
                    <a href="{{ route('adminuniversity.periodes') }}" class="btn btn-xs btn-primary">Gérer</a>
                </div>
                <div class="card-body">
                    <div class="pb-3 border-bottom mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="font-w500 fs-14">S2 2025-2026</span>
                            <span class="badge badge-sm badge-success">Active</span>
                        </div>
                        <small class="text-muted">01/02/2026 → 31/05/2026</small>
                        <div class="progress mt-2" style="height:5px;">
                            <div class="progress-bar bg-success" style="width:65%"></div>
                        </div>
                        <small class="text-muted">65% de la période écoulée</small>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="font-w500 fs-14">Rattrapage Juin 2026</span>
                            <span class="badge badge-sm badge-warning">À venir</span>
                        </div>
                        <small class="text-muted">01/06/2026 → 15/06/2026</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Accès rapide --}}
    <div class="row">
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('adminuniversity.etudiants') }}" class="card text-decoration-none">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded" style="width:48px;height:48px;">
                        <i class="flaticon-381-user-9 fs-20"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Gérer les étudiants</h5>
                        <small class="text-muted">CRUD + import CSV</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('adminuniversity.enseignants') }}" class="card text-decoration-none">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="d-flex align-items-center justify-content-center bg-success text-white rounded" style="width:48px;height:48px;">
                        <i class="flaticon-381-television fs-20"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Gérer les enseignants</h5>
                        <small class="text-muted">CRUD complet</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('adminuniversity.annexes') }}" class="card text-decoration-none">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="d-flex align-items-center justify-content-center bg-info text-white rounded" style="width:48px;height:48px;">
                        <i class="flaticon-381-map-2 fs-20"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Gérer les annexes</h5>
                        <small class="text-muted">Sites &amp; gestionnaires</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('adminuniversity.rapports') }}" class="card text-decoration-none">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="d-flex align-items-center justify-content-center bg-warning text-white rounded" style="width:48px;height:48px;">
                        <i class="flaticon-381-notepad fs-20"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Exporter un rapport</h5>
                        <small class="text-muted">PDF consolidé</small>
                    </div>
                </div>
            </a>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('dashboard/vendor/chart-js/chart.bundle.min.js') }}"></script>
<script src="{{ asset('dashboard/vendor/waypoints/jquery.waypoints.min.js') }}"></script>
<script src="{{ asset('dashboard/vendor/jquery.counterup/jquery.counterup.min.js') }}"></script>
<script>
    jQuery('.counter').counterUp({ delay: 10, time: 1000 });

    new Chart(document.getElementById('chartFormations'), {
        type: 'bar',
        data: {
            labels: ['L3 Info', 'M1 Maths', 'M2 IA', 'L2 Phys', 'M1 Data', 'L3 Chimie', 'M2 Réseaux'],
            datasets: [{
                label: 'Évaluations',
                data: [72, 65, 58, 48, 61, 43, 65],
                backgroundColor: '#2F4CDD',
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
@endpush
