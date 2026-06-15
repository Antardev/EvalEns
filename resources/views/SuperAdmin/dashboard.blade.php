@extends('layouts.superadmin')

@section('title', 'Tableau de bord')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Tableau de bord</h2>
            <p class="mb-0">Vue d'ensemble de la plateforme ÉvalENS</p>
        </div>
        <div class="d-flex align-items-center">
            <span class="text-muted me-2 fs-14">{{ now()->format('d/m/Y') }}</span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="row">
        <div class="col-xl-3 col-xxl-3 col-lg-6 col-md-6 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon d-flex">
                        <span class="me-3 bgl-primary text-primary">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#2F4CDD" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                            </svg>
                        </span>
                        <div class="media-body">
                            <h3 class="mb-0 text-black"><span class="counter ms-0">12</span></h3>
                            <p class="mb-0">Universités</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-xxl-3 col-lg-6 col-md-6 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon d-flex">
                        <span class="me-3 bgl-success text-success">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#2BC155" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </span>
                        <div class="media-body">
                            <h3 class="mb-0 text-black"><span class="counter ms-0">248</span></h3>
                            <p class="mb-0">Utilisateurs</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-xxl-3 col-lg-6 col-md-6 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon d-flex">
                        <span class="me-3 bgl-warning text-warning">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#FFAB2D" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                        </span>
                        <div class="media-body">
                            <h3 class="mb-0 text-black"><span class="counter ms-0">1842</span></h3>
                            <p class="mb-0">Évaluations</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-xxl-3 col-lg-6 col-md-6 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon d-flex">
                        <span class="me-3 bgl-danger text-danger">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#FF2E2E" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </span>
                        <div class="media-body">
                            <h3 class="mb-0 text-black"><span class="counter ms-0">3</span></h3>
                            <p class="mb-0">Demandes en attente</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row">
        <div class="col-xl-8 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0 d-sm-flex flex-wrap d-block">
                    <div class="mb-3">
                        <h4 class="card-title mb-1">Évaluations par mois</h4>
                        <small class="mb-0">Évolution sur les 6 derniers mois</small>
                    </div>
                    <div class="card-action card-tabs mb-2">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#evol-6mois" role="tab">6 mois</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#evol-annee" role="tab">Année</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="evol-6mois">
                            <canvas id="chartEvaluations" height="100"></canvas>
                        </div>
                        <div class="tab-pane fade" id="evol-annee">
                            <canvas id="chartEvaluationsAnnee" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Top 5 Universités</h4>
                    <small class="mb-0">Répartition des évaluations</small>
                </div>
                <div class="card-body">
                    <canvas id="chartUniversites" height="200"></canvas>
                    <div class="mt-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fs-13"><span class="d-inline-block me-2" style="width:12px;height:12px;border-radius:2px;background:#2F4CDD;"></span>Univ. Paris-Saclay</span>
                            <span class="badge badge-sm badge-primary">412</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fs-13"><span class="d-inline-block me-2" style="width:12px;height:12px;border-radius:2px;background:#2BC155;"></span>Sorbonne Université</span>
                            <span class="badge badge-sm badge-success">387</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fs-13"><span class="d-inline-block me-2" style="width:12px;height:12px;border-radius:2px;background:#FFAB2D;"></span>ENS Lyon</span>
                            <span class="badge badge-sm badge-warning">301</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fs-13"><span class="d-inline-block me-2" style="width:12px;height:12px;border-radius:2px;background:#FF2E2E;"></span>Univ. Bordeaux</span>
                            <span class="badge badge-sm badge-danger">278</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="fs-13"><span class="d-inline-block me-2" style="width:12px;height:12px;border-radius:2px;background:#6c757d;"></span>Autres</span>
                            <span class="badge badge-sm badge-secondary">464</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Activité récente & Inscriptions en attente --}}
    <div class="row">
        <div class="col-xl-7 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title">Activité récente</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Utilisateur</th>
                                    <th>Action</th>
                                    <th>Université</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="font-w500">Marie Dupont</span></td>
                                    <td><span class="badge badge-sm badge-primary">Évaluation soumise</span></td>
                                    <td>Paris-Saclay</td>
                                    <td class="text-muted fs-13">Il y a 5 min</td>
                                </tr>
                                <tr>
                                    <td><span class="font-w500">Jean Martin</span></td>
                                    <td><span class="badge badge-sm badge-success">Inscription approuvée</span></td>
                                    <td>ENS Lyon</td>
                                    <td class="text-muted fs-13">Il y a 23 min</td>
                                </tr>
                                <tr>
                                    <td><span class="font-w500">Sophie Leroy</span></td>
                                    <td><span class="badge badge-sm badge-warning">Période ouverte</span></td>
                                    <td>Sorbonne</td>
                                    <td class="text-muted fs-13">Il y a 1h</td>
                                </tr>
                                <tr>
                                    <td><span class="font-w500">Paul Bernard</span></td>
                                    <td><span class="badge badge-sm badge-danger">Inscription rejetée</span></td>
                                    <td>Bordeaux</td>
                                    <td class="text-muted fs-13">Il y a 2h</td>
                                </tr>
                                <tr>
                                    <td><span class="font-w500">Claire Moreau</span></td>
                                    <td><span class="badge badge-sm badge-info">Rapport exporté</span></td>
                                    <td>Paris-Saclay</td>
                                    <td class="text-muted fs-13">Il y a 3h</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 text-end">
                        <a href="{{ route('superadmin.logs') }}" class="btn btn-sm btn-outline-primary">Voir tous les logs</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-5 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0 d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Demandes en attente</h4>
                    <a href="{{ route('superadmin.inscriptions') }}" class="btn btn-xs btn-primary">Gérer</a>
                </div>
                <div class="card-body">
                    <div class="media d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="me-3">
                            <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-circle" style="width:42px;height:42px;font-size:16px;font-weight:600;">L</div>
                        </div>
                        <div class="media-body flex-grow-1">
                            <h6 class="mb-0 font-w500">Lucas Fontaine</h6>
                            <small class="text-muted">Univ. de Rennes · Étudiant</small>
                        </div>
                        <span class="badge badge-sm badge-warning">En attente</span>
                    </div>
                    <div class="media d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="me-3">
                            <div class="d-flex align-items-center justify-content-center bg-success text-white rounded-circle" style="width:42px;height:42px;font-size:16px;font-weight:600;">A</div>
                        </div>
                        <div class="media-body flex-grow-1">
                            <h6 class="mb-0 font-w500">Amina Chérif</h6>
                            <small class="text-muted">ENS Paris · Enseignant</small>
                        </div>
                        <span class="badge badge-sm badge-warning">En attente</span>
                    </div>
                    <div class="media d-flex align-items-center">
                        <div class="me-3">
                            <div class="d-flex align-items-center justify-content-center bg-warning text-white rounded-circle" style="width:42px;height:42px;font-size:16px;font-weight:600;">T</div>
                        </div>
                        <div class="media-body flex-grow-1">
                            <h6 class="mb-0 font-w500">Thomas Girard</h6>
                            <small class="text-muted">Univ. Strasbourg · Étudiant</small>
                        </div>
                        <span class="badge badge-sm badge-warning">En attente</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('dashboard/vendor/chart-js/chart.bundle.min.js') }}"></script>
<script src="{{ asset('dashboard/vendor/waypoints/jquery.waypoints.min.js') }}"></script>
<script src="{{ asset('dashboard/vendor/jquery.counterup/jquery.counterup.min.js') }}"></script>
<script>
    // Counter animation
    jQuery('.counter').counterUp({ delay: 10, time: 1000 });

    // Bar chart: évaluations par mois
    var ctx1 = document.getElementById('chartEvaluations').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ['Décembre', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai'],
            datasets: [{
                label: 'Évaluations',
                data: [285, 320, 298, 410, 376, 353],
                backgroundColor: 'rgba(47, 76, 221, 0.8)',
                borderColor: '#2F4CDD',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 100 } } }
        }
    });

    // Bar chart annuel
    var ctx1b = document.getElementById('chartEvaluationsAnnee').getContext('2d');
    new Chart(ctx1b, {
        type: 'line',
        data: {
            labels: ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Aoû','Sep','Oct','Nov','Déc'],
            datasets: [{
                label: 'Évaluations',
                data: [320, 298, 410, 376, 353, 430, 180, 90, 440, 510, 490, 285],
                borderColor: '#2F4CDD',
                backgroundColor: 'rgba(47, 76, 221, 0.1)',
                tension: 0.4,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Doughnut chart: top universités
    var ctx2 = document.getElementById('chartUniversites').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Paris-Saclay', 'Sorbonne', 'ENS Lyon', 'Bordeaux', 'Autres'],
            datasets: [{
                data: [412, 387, 301, 278, 464],
                backgroundColor: ['#2F4CDD','#2BC155','#FFAB2D','#FF2E2E','#6c757d'],
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            cutout: '65%',
        }
    });
</script>
@endpush
