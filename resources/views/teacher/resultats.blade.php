@extends('layouts.app')

@section('title', 'Mes résultats')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Mes résultats</h2>
            <p class="mb-0">Statistiques agrégées — données anonymisées</p>
        </div>
        <select class="form-select form-select-sm w-auto">
            <option>S2 2025-2026 (en cours)</option>
            <option>S1 2025-2026</option>
            <option>S2 2024-2025</option>
        </select>
    </div>

    <div class="alert alert-info fs-13">
        <i class="lni lni-lock-alt me-2"></i>
        Ces résultats sont des <strong>statistiques agrégées et anonymisées</strong>.
        Ils ne sont affichés que si au moins 5 réponses ont été collectées par UE.
    </div>

    {{-- Résumé global --}}
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card text-center bg-primary text-white">
                <div class="card-body py-4">
                    <h2 class="font-w700 mb-1">4.2<small class="fs-16">/5</small></h2>
                    <p class="mb-0 opacity-75">Note moyenne globale</p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card text-center">
                <div class="card-body py-4">
                    <h2 class="font-w700 mb-1 text-success">68</h2>
                    <p class="mb-0 text-muted">Évaluations reçues au total</p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card text-center">
                <div class="card-body py-4">
                    <h2 class="font-w700 mb-1 text-warning">89%</h2>
                    <p class="mb-0 text-muted">Taux de participation moyen</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Détail par critère --}}
    <div class="card mb-4">
        <div class="card-header border-0 pb-0">
            <h4 class="card-title mb-1">Score par critère d'évaluation</h4>
            <small class="text-muted">Comparaison avec la moyenne de l'université</small>
        </div>
        <div class="card-body">
            @php
            $criteres = [
                ['Qualité pédagogique',    4.4, 4.0],
                ['Organisation du cours',  4.0, 3.8],
                ['Communication',          4.3, 3.9],
                ['Disponibilité',          3.9, 3.7],
                ["Équité de l'évaluation", 4.2, 3.8],
            ];
            @endphp
            @foreach($criteres as [$nom, $moi, $moy])
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="font-w500 fs-14">{{ $nom }}</span>
                    <div class="d-flex gap-3">
                        <span class="text-primary fs-13 font-w600">Moi : {{ $moi }}/5</span>
                        <span class="text-muted fs-13">Moy. univ : {{ $moy }}/5</span>
                    </div>
                </div>
                <div class="position-relative" style="height:10px;">
                    <div class="progress w-100" style="height:10px;opacity:0.3;position:absolute;">
                        <div class="progress-bar bg-warning" style="width:{{ $moy * 20 }}%"></div>
                    </div>
                    <div class="progress w-100" style="height:10px;position:absolute;">
                        <div class="progress-bar bg-primary" style="width:{{ $moi * 20 }}%"></div>
                    </div>
                </div>
            </div>
            @endforeach
            <div class="d-flex gap-4 mt-3 fs-12 text-muted">
                <span><span class="d-inline-block bg-primary rounded me-1" style="width:12px;height:12px;"></span>Mes scores</span>
                <span><span class="d-inline-block rounded me-1" style="width:12px;height:12px;background:#FFAB2D;opacity:0.5;"></span>Moy. université</span>
            </div>
        </div>
    </div>

    {{-- Détail par UE --}}
    <div class="card">
        <div class="card-header border-0 pb-0">
            <h4 class="card-title mb-1">Résultats par unité d'enseignement</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>UE</th>
                            <th>Formation</th>
                            <th class="text-center">Évaluations</th>
                            <th class="text-center">Note globale</th>
                            <th class="text-center">Pédagogie</th>
                            <th class="text-center">Organisation</th>
                            <th class="text-center">Communication</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-w500">Algorithmique avancée</td>
                            <td>L3 Informatique</td>
                            <td class="text-center">28</td>
                            <td class="text-center"><span class="badge badge-success">4.4/5</span></td>
                            <td class="text-center">4.6</td>
                            <td class="text-center">4.2</td>
                            <td class="text-center">4.3</td>
                        </tr>
                        <tr>
                            <td class="font-w500">Deep Learning</td>
                            <td>M2 IA</td>
                            <td class="text-center">15</td>
                            <td class="text-center"><span class="badge badge-success">4.1/5</span></td>
                            <td class="text-center">4.3</td>
                            <td class="text-center">3.9</td>
                            <td class="text-center">4.0</td>
                        </tr>
                        <tr>
                            <td class="font-w500">Traitement du langage naturel</td>
                            <td>M2 IA</td>
                            <td class="text-center">15</td>
                            <td class="text-center"><span class="badge badge-success">4.2/5</span></td>
                            <td class="text-center">4.4</td>
                            <td class="text-center">4.0</td>
                            <td class="text-center">4.3</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
