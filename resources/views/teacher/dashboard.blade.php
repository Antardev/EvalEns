@extends('layouts.app')

@section('title', 'Mon tableau de bord')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Bonjour, {{ auth()->user()->prenom ?? 'Enseignant' }}</h2>
            <p class="mb-0">Vos résultats d'évaluation — données anonymisées</p>
        </div>
        <a href="{{ route('teacher.resultats') }}" class="btn btn-outline-primary btn-sm">
            <i class="lni lni-bar-chart me-1"></i>Voir le détail
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
                            @if($moyenneGlobale)
                                <h3 class="mb-0 text-black">{{ round($moyenneGlobale * 20) }}<small class="fs-14">%</small></h3>
                            @else
                                <h3 class="mb-0 text-muted">—</h3>
                            @endif
                            <p class="mb-0">Satisfaction moyenne</p>
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
                            <h3 class="mb-0 text-black">{{ $totalReponses }}</h3>
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
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#FFAB2D" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </span>
                        <div class="media-body">
                            <h3 class="mb-0 text-black">{{ $liens->count() }}</h3>
                            <p class="mb-0">Questionnaires total</p>
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
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#FF2E2E" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </span>
                        <div class="media-body">
                            <h3 class="mb-0 text-black">
                                {{ $liens->sum(fn($l) => $l->reponses->filter(fn($r) => $r->commentaire)->count()) }}
                            </h3>
                            <p class="mb-0">Commentaires reçus</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Score par critère --}}
        <div class="col-xl-5 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Score par critère</h4>
                    <small class="text-muted">Moyenne sur tous vos questionnaires</small>
                </div>
                <div class="card-body">
                    @if(empty($parCritere))
                        <p class="text-muted text-center py-4">Aucune évaluation reçue pour le moment.</p>
                    @else
                        @foreach($parCritere as $label => $score)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fs-13 font-w500">{{ $label }}</span>
                                <span class="fs-13 font-w600 text-primary">{{ round($score * 20) }}%</span>
                            </div>
                            <div class="progress" style="height:8px;">
                                <div class="progress-bar
                                    {{ $score >= 4 ? 'bg-success' : ($score >= 3 ? 'bg-primary' : ($score >= 2 ? 'bg-warning' : 'bg-danger')) }}"
                                    style="width:{{ $score * 20 }}%">
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @if($moyenneGlobale)
                        <canvas id="chartRadar" height="260" class="mt-3"></canvas>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- Derniers questionnaires --}}
        <div class="col-xl-7 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Mes questionnaires récents</h4>
                    <small class="text-muted">Vos derniers liens de questionnaire</small>
                </div>
                <div class="card-body p-0">
                    @if($derniersLiens->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="lni lni-clipboard fs-1 d-block mb-2"></i>
                            Aucun questionnaire vous concernant pour le moment.
                        </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Titre / Classe</th>
                                    <th class="text-center">Réponses</th>
                                    <th class="text-center">Satisfaction</th>
                                    <th class="text-center">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($derniersLiens as $lien)
                                @php
                                    $scores = [];
                                    foreach ($lien->reponses as $r) {
                                        foreach ($r->scores as $s) { $scores[] = $s['score']; }
                                    }
                                    $moy = count($scores) ? round(array_sum($scores) / count($scores) * 20) : null;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="font-w500">{{ $lien->titre }}</div>
                                        <small class="text-muted">{{ $lien->classe }}{{ $lien->matiere ? ' · '.$lien->matiere : '' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $lien->reponses_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($moy !== null)
                                            <span class="badge {{ $moy >= 80 ? 'badge-success' : ($moy >= 60 ? 'badge-primary' : ($moy >= 40 ? 'badge-warning' : 'badge-danger')) }}">
                                                {{ $moy }}%
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($lien->statut === 'actif')
                                            <span class="badge badge-success">Actif</span>
                                        @else
                                            <span class="badge badge-secondary">Fermé</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 text-end">
                        <a href="{{ route('teacher.resultats') }}" class="btn btn-sm btn-outline-primary">
                            Voir tous les résultats <i class="lni lni-arrow-right ms-1"></i>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('dashboard/vendor/chart-js/chart.bundle.min.js') }}"></script>
@if(!empty($parCritere) && $moyenneGlobale)
<script>
new Chart(document.getElementById('chartRadar'), {
    type: 'radar',
    data: {
        labels: {!! json_encode(array_keys($parCritere)) !!},
        datasets: [{
            label: 'Mes scores (%)',
            data: {!! json_encode(array_map(fn($s) => round($s * 20), array_values($parCritere))) !!},
            borderColor: '#2F4CDD',
            backgroundColor: 'rgba(47,76,221,0.15)',
            pointBackgroundColor: '#2F4CDD',
        }]
    },
    options: {
        responsive: true,
        scales: { r: { min: 0, max: 100, ticks: { stepSize: 20, callback: v => v + '%' } } },
        plugins: { legend: { display: false } }
    }
});
</script>
@endif
@endpush
