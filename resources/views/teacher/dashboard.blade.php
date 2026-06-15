@extends('layouts.app')

@section('title', 'Mon tableau de bord')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Bonjour, {{ auth()->user()->name ?? 'Enseignant' }}</h2>
            <p class="mb-0">Vos résultats — Période : S2 2025-2026</p>
        </div>
        <a href="{{ route('teacher.rapport') }}" class="btn btn-outline-primary btn-sm">
            <i class="lni lni-download me-1"></i>Exporter mon rapport
        </a>
    </div>

    {{-- KPI Cards --}}
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon d-flex">
                        <span class="me-3 bgl-primary text-primary">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#2F4CDD" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        </span>
                        <div class="media-body">
                            <h3 class="mb-0 text-black">4.2<small class="fs-14">/5</small></h3>
                            <p class="mb-0">Note moyenne</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon d-flex">
                        <span class="me-3 bgl-success text-success">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#2BC155" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </span>
                        <div class="media-body">
                            <h3 class="mb-0 text-black"><span class="counter ms-0">68</span></h3>
                            <p class="mb-0">Évaluations reçues</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon d-flex">
                        <span class="me-3 bgl-warning text-warning">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#FFAB2D" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                        </span>
                        <div class="media-body">
                            <h3 class="mb-0 text-black">&#8593; +0.3</h3>
                            <p class="mb-0">Tendance vs S1</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon d-flex">
                        <span class="me-3 bgl-danger text-danger">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#FF2E2E" stroke-width="2"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                        </span>
                        <div class="media-body">
                            <h3 class="mb-0 text-black">#3</h3>
                            <p class="mb-0">Rang dans l'université</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Radar des critères --}}
        <div class="col-xl-5 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Score par critère</h4>
                    <small class="text-muted">Période S2 2025-2026</small>
                </div>
                <div class="card-body">
                    <canvas id="chartRadar" height="220"></canvas>
                </div>
            </div>
        </div>

        {{-- UEs avec notes --}}
        <div class="col-xl-7 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Résultats par UE</h4>
                    <small class="text-muted">Vos unités d'enseignement ce semestre</small>
                </div>
                <div class="card-body">
                    @php
                    $ues = [
                        ['Algorithmique avancée', 'L3 Informatique', 4.4, 28, 89],
                        ['Deep Learning', 'M2 IA', 4.1, 15, 93],
                        ['Traitement du langage', 'M2 IA', 4.2, 15, 87],
                    ];
                    @endphp
                    @foreach($ues as [$nomUE, $formation, $note, $nbEval, $taux])
                    <div class="d-flex align-items-start mb-4 {{ !$loop->last ? 'pb-4 border-bottom' : '' }}">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 font-w500">{{ $nomUE }}</h6>
                                <span class="badge badge-sm badge-{{ $note >= 4 ? 'success' : 'warning' }}">{{ $note }}/5</span>
                            </div>
                            <small class="text-muted d-block mb-2">{{ $formation }} · {{ $nbEval }} évaluations · {{ $taux }}% participation</small>
                            <div class="progress" style="height:6px;">
                                <div class="progress-bar bg-primary" style="width:{{ $note * 20 }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div class="text-center mt-2">
                        <a href="{{ route('teacher.resultats') }}" class="btn btn-sm btn-outline-primary">Voir le détail complet</a>
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
    jQuery('.counter').counterUp({ delay: 10, time: 1000 });

    new Chart(document.getElementById('chartRadar'), {
        type: 'radar',
        data: {
            labels: ['Pédagogie', 'Organisation', 'Communication', 'Disponibilité', 'Équité'],
            datasets: [
                {
                    label: 'Mes scores',
                    data: [4.4, 4.0, 4.3, 3.9, 4.2],
                    borderColor: '#2F4CDD',
                    backgroundColor: 'rgba(47,76,221,0.15)',
                    pointBackgroundColor: '#2F4CDD',
                },
                {
                    label: 'Moy. université',
                    data: [4.0, 3.8, 3.9, 3.7, 3.8],
                    borderColor: '#FFAB2D',
                    backgroundColor: 'rgba(255,171,45,0.1)',
                    borderDash: [5,3],
                    pointBackgroundColor: '#FFAB2D',
                }
            ]
        },
        options: {
            responsive: true,
            scales: { r: { min: 0, max: 5, ticks: { stepSize: 1 } } },
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>
@endpush
