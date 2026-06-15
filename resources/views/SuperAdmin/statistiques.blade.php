@extends('layouts.superadmin')

@section('title', 'Statistiques globales')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Statistiques globales</h2>
            <p class="mb-0">Vue analytique de la plateforme ÉvalENS</p>
        </div>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" id="filterAnnee">
                <option value="2026">2025–2026</option>
                <option value="2025">2024–2025</option>
                <option value="2024">2023–2024</option>
            </select>
            <select class="form-select form-select-sm" id="filterSemestre">
                <option value="">Toute l'année</option>
                <option value="S1">Semestre 1</option>
                <option value="S2">Semestre 2</option>
            </select>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="widget-stat card bg-primary">
                <div class="card-body p-4">
                    <div class="media ai-icon d-flex">
                        <span class="me-3">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        </span>
                        <div class="media-body">
                            <h3 class="mb-0 text-white"><span class="counter ms-0">3.8</span>/5</h3>
                            <p class="mb-0 text-white opacity-75">Note moyenne globale</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="widget-stat card bg-success">
                <div class="card-body p-4">
                    <div class="media ai-icon d-flex">
                        <span class="me-3">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </span>
                        <div class="media-body">
                            <h3 class="mb-0 text-white"><span class="counter ms-0">78</span>%</h3>
                            <p class="mb-0 text-white opacity-75">Taux de participation</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="widget-stat card bg-warning">
                <div class="card-body p-4">
                    <div class="media ai-icon d-flex">
                        <span class="me-3">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        </span>
                        <div class="media-body">
                            <h3 class="mb-0 text-white"><span class="counter ms-0">156</span></h3>
                            <p class="mb-0 text-white opacity-75">Enseignants évalués</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="widget-stat card bg-danger">
                <div class="card-body p-4">
                    <div class="media ai-icon d-flex">
                        <span class="me-3">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </span>
                        <div class="media-body">
                            <h3 class="mb-0 text-white"><span class="counter ms-0">8</span></h3>
                            <p class="mb-0 text-white opacity-75">Périodes en cours</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphiques --}}
    <div class="row">
        <div class="col-xl-8 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Évolution des évaluations</h4>
                    <small class="text-muted">Nombre d'évaluations soumises par mois</small>
                </div>
                <div class="card-body">
                    <canvas id="chartEvolution" height="90"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Distribution des notes</h4>
                    <small class="text-muted">Répartition globale 1–5</small>
                </div>
                <div class="card-body">
                    <canvas id="chartDistribution" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Note moyenne par université</h4>
                    <small class="text-muted">Comparaison inter-établissements</small>
                </div>
                <div class="card-body">
                    <canvas id="chartParUniversite" height="130"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Score moyen par critère</h4>
                    <small class="text-muted">Performance sur les 5 critères principaux</small>
                </div>
                <div class="card-body">
                    <canvas id="chartParCritere" height="130"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau récapitulatif par université --}}
    <div class="card">
        <div class="card-header border-0 pb-0">
            <h4 class="card-title mb-1">Récapitulatif par université</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Université</th>
                            <th class="text-center">Évaluations</th>
                            <th class="text-center">Participation</th>
                            <th class="text-center">Note moyenne</th>
                            <th class="text-center">Tendance</th>
                            <th class="text-center">Meilleur critère</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-w500">Univ. Paris-Saclay</td>
                            <td class="text-center">412</td>
                            <td class="text-center">
                                <div class="progress" style="height:6px;width:80px;display:inline-flex;">
                                    <div class="progress-bar bg-success" style="width:85%"></div>
                                </div>
                                <span class="ms-1 fs-12">85%</span>
                            </td>
                            <td class="text-center"><span class="badge badge-sm badge-success">4.1/5</span></td>
                            <td class="text-center text-success">▲ +0.2</td>
                            <td class="text-center text-muted fs-12">Pédagogie</td>
                        </tr>
                        <tr>
                            <td class="font-w500">Sorbonne Université</td>
                            <td class="text-center">387</td>
                            <td class="text-center">
                                <div class="progress" style="height:6px;width:80px;display:inline-flex;">
                                    <div class="progress-bar bg-success" style="width:79%"></div>
                                </div>
                                <span class="ms-1 fs-12">79%</span>
                            </td>
                            <td class="text-center"><span class="badge badge-sm badge-success">3.9/5</span></td>
                            <td class="text-center text-success">▲ +0.1</td>
                            <td class="text-center text-muted fs-12">Communication</td>
                        </tr>
                        <tr>
                            <td class="font-w500">ENS Lyon</td>
                            <td class="text-center">301</td>
                            <td class="text-center">
                                <div class="progress" style="height:6px;width:80px;display:inline-flex;">
                                    <div class="progress-bar bg-warning" style="width:72%"></div>
                                </div>
                                <span class="ms-1 fs-12">72%</span>
                            </td>
                            <td class="text-center"><span class="badge badge-sm badge-warning">3.7/5</span></td>
                            <td class="text-center text-danger">▼ -0.1</td>
                            <td class="text-center text-muted fs-12">Organisation</td>
                        </tr>
                        <tr>
                            <td class="font-w500">Univ. Bordeaux</td>
                            <td class="text-center">278</td>
                            <td class="text-center">
                                <div class="progress" style="height:6px;width:80px;display:inline-flex;">
                                    <div class="progress-bar bg-success" style="width:81%"></div>
                                </div>
                                <span class="ms-1 fs-12">81%</span>
                            </td>
                            <td class="text-center"><span class="badge badge-sm badge-success">3.8/5</span></td>
                            <td class="text-center text-muted">— 0.0</td>
                            <td class="text-center text-muted fs-12">Disponibilité</td>
                        </tr>
                    </tbody>
                </table>
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
    jQuery('.counter').counterUp({ delay: 10, time: 1000 });

    // Évolution
    new Chart(document.getElementById('chartEvolution'), {
        type: 'line',
        data: {
            labels: ['Sep','Oct','Nov','Déc','Jan','Fév','Mar','Avr','Mai'],
            datasets: [
                {
                    label: 'Évaluations soumises',
                    data: [440, 510, 490, 285, 320, 298, 410, 376, 353],
                    borderColor: '#2F4CDD', backgroundColor: 'rgba(47,76,221,0.1)',
                    tension: 0.4, fill: true,
                },
                {
                    label: 'Objectif',
                    data: [400, 400, 450, 450, 400, 350, 400, 400, 400],
                    borderColor: '#FFAB2D', borderDash: [5, 5],
                    tension: 0, fill: false, borderWidth: 1.5,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: false, min: 0 } }
        }
    });

    // Distribution des notes
    new Chart(document.getElementById('chartDistribution'), {
        type: 'bar',
        data: {
            labels: ['Note 1', 'Note 2', 'Note 3', 'Note 4', 'Note 5'],
            datasets: [{
                label: 'Évaluations',
                data: [42, 138, 487, 712, 463],
                backgroundColor: ['#FF2E2E','#FF8C00','#FFAB2D','#2BC155','#2F4CDD'],
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Par université
    new Chart(document.getElementById('chartParUniversite'), {
        type: 'bar',
        data: {
            labels: ['Paris-Saclay', 'Sorbonne', 'ENS Lyon', 'Bordeaux', 'Rennes', 'Strasbourg'],
            datasets: [{
                label: 'Note moyenne',
                data: [4.1, 3.9, 3.7, 3.8, 3.6, 3.5],
                backgroundColor: '#2F4CDD',
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { min: 0, max: 5, ticks: { stepSize: 1 } } }
        }
    });

    // Par critère (radar)
    new Chart(document.getElementById('chartParCritere'), {
        type: 'radar',
        data: {
            labels: ['Pédagogie', 'Organisation', 'Communication', 'Disponibilité', 'Équité'],
            datasets: [{
                label: 'Score moyen',
                data: [4.0, 3.7, 3.9, 3.5, 3.8],
                borderColor: '#2F4CDD',
                backgroundColor: 'rgba(47,76,221,0.15)',
                pointBackgroundColor: '#2F4CDD',
            }]
        },
        options: {
            responsive: true,
            scales: { r: { min: 0, max: 5, ticks: { stepSize: 1 } } },
            plugins: { legend: { display: false } }
        }
    });
</script>
@endpush
