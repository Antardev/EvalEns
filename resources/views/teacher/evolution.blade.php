@extends('layouts.app')

@section('title', 'Évolution dans le temps')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Évolution dans le temps</h2>
            <p class="mb-0">Progression de vos évaluations sur plusieurs semestres</p>
        </div>
    </div>

    {{-- Graphique principal --}}
    <div class="card mb-4">
        <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="card-title mb-1">Note moyenne par semestre</h4>
                <small class="text-muted">Évolution sur 4 semestres</small>
            </div>
            <select class="form-select form-select-sm w-auto" id="filterUE">
                <option value="all">Toutes les UE</option>
                <option value="algo">Algorithmique avancée</option>
                <option value="dl">Deep Learning</option>
                <option value="nlp">Traitement du langage</option>
            </select>
        </div>
        <div class="card-body">
            <canvas id="chartEvolution" height="80"></canvas>
        </div>
    </div>

    <div class="row">
        {{-- Évolution par critère --}}
        <div class="col-xl-7 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Évolution par critère</h4>
                </div>
                <div class="card-body">
                    <canvas id="chartCriteres" height="130"></canvas>
                </div>
            </div>
        </div>

        {{-- Tableau récapitulatif --}}
        <div class="col-xl-5 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Récapitulatif</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Semestre</th>
                                    <th class="text-center">Note</th>
                                    <th class="text-center">Éval.</th>
                                    <th class="text-center">Tendance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>S2 2025-26 <span class="badge badge-xs badge-success ms-1">En cours</span></td>
                                    <td class="text-center"><strong>4.2</strong></td>
                                    <td class="text-center">68</td>
                                    <td class="text-center text-success">▲ +0.3</td>
                                </tr>
                                <tr>
                                    <td>S1 2025-26</td>
                                    <td class="text-center">3.9</td>
                                    <td class="text-center">72</td>
                                    <td class="text-center text-success">▲ +0.2</td>
                                </tr>
                                <tr>
                                    <td>S2 2024-25</td>
                                    <td class="text-center">3.7</td>
                                    <td class="text-center">65</td>
                                    <td class="text-center text-danger">▼ -0.1</td>
                                </tr>
                                <tr>
                                    <td>S1 2024-25</td>
                                    <td class="text-center">3.8</td>
                                    <td class="text-center">58</td>
                                    <td class="text-center text-muted">—</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 p-3 bg-light rounded">
                        <p class="fs-13 mb-1"><strong>Progression globale :</strong></p>
                        <p class="fs-13 text-muted mb-0">
                            Depuis S1 2024-25, votre note a progressé de
                            <span class="text-success font-w600">+0.4 point</span>,
                            plaçant vos résultats au-dessus de la moyenne universitaire.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('dashboard/vendor/chart-js/chart.bundle.min.js') }}"></script>
<script>
    var labels = ['S1 2024-25', 'S2 2024-25', 'S1 2025-26', 'S2 2025-26'];

    new Chart(document.getElementById('chartEvolution'), {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Mes notes',
                    data: [3.8, 3.7, 3.9, 4.2],
                    borderColor: '#2F4CDD',
                    backgroundColor: 'rgba(47,76,221,0.1)',
                    tension: 0.4, fill: true, pointRadius: 5,
                },
                {
                    label: 'Moy. université',
                    data: [3.7, 3.6, 3.8, 4.0],
                    borderColor: '#FFAB2D',
                    borderDash: [5, 3],
                    tension: 0.4, fill: false, pointRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            scales: { y: { min: 2, max: 5, ticks: { stepSize: 0.5 } } },
            plugins: { legend: { position: 'top' } }
        }
    });

    new Chart(document.getElementById('chartCriteres'), {
        type: 'line',
        data: {
            labels,
            datasets: [
                { label: 'Pédagogie',     data: [3.9, 3.8, 4.1, 4.4], borderColor: '#2F4CDD', tension: 0.4, fill: false },
                { label: 'Organisation',  data: [3.7, 3.5, 3.8, 4.0], borderColor: '#2BC155', tension: 0.4, fill: false },
                { label: 'Communication', data: [3.8, 3.7, 4.0, 4.3], borderColor: '#FFAB2D', tension: 0.4, fill: false },
                { label: 'Disponibilité', data: [3.6, 3.6, 3.7, 3.9], borderColor: '#FF2E2E', tension: 0.4, fill: false },
            ]
        },
        options: {
            responsive: true,
            scales: { y: { min: 2, max: 5 } },
            plugins: { legend: { position: 'right', labels: { font: { size: 11 } } } }
        }
    });
</script>
@endpush
