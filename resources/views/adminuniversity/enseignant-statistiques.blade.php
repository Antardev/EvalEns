@extends('layouts.app')

@section('title', 'Statistiques — ' . $enseignant->prenom . ' ' . $enseignant->nom)

@section('content')
<div class="container-fluid">

    {{-- Fil d'Ariane --}}
    <div class="form-head d-flex mb-3 align-items-center gap-2">
        <a href="{{ route('adminuniversity.enseignants') }}" class="btn btn-sm btn-outline-secondary">
            <i class="lni lni-arrow-left me-1"></i>Retour
        </a>
        <div>
            <h2 class="text-primary font-w600 mb-0 fs-18">
                {{ $enseignant->prenom }} {{ $enseignant->nom }}
            </h2>
            <p class="mb-0 fs-13 text-muted">{{ $enseignant->email }}</p>
        </div>
    </div>

    {{-- Annexes rattachées --}}
    <div class="mb-3 d-flex flex-wrap gap-2">
        @foreach($enseignant->annexes as $annexe)
            <span class="badge badge-success fs-12">
                <i class="lni lni-home me-1"></i>{{ $annexe->nom }}
            </span>
        @endforeach
    </div>

    {{-- KPI --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <span class="bgl-primary text-primary" style="width:44px;height:44px;display:flex;align-items:center;justify-content:center;border-radius:8px;flex-shrink:0;">
                        <i class="lni lni-files" style="font-size:20px;"></i>
                    </span>
                    <div>
                        <h3 class="mb-0 text-black fw-bold">{{ $totalLiens }}</h3>
                        <p class="mb-0 fs-13 text-muted">Questionnaires créés</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <span class="bgl-success text-success" style="width:44px;height:44px;display:flex;align-items:center;justify-content:center;border-radius:8px;flex-shrink:0;">
                        <i class="lni lni-checkmark-circle" style="font-size:20px;"></i>
                    </span>
                    <div>
                        <h3 class="mb-0 text-black fw-bold">{{ $totalReponses }}</h3>
                        <p class="mb-0 fs-13 text-muted">Évaluations reçues</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <span class="bgl-warning text-warning" style="width:44px;height:44px;display:flex;align-items:center;justify-content:center;border-radius:8px;flex-shrink:0;">
                        <i class="lni lni-star" style="font-size:20px;"></i>
                    </span>
                    <div>
                        @if($moyenneGlobale !== null)
                            <h3 class="mb-0 text-black fw-bold">{{ number_format($moyenneGlobale, 2) }}<small class="fs-13 text-muted">/5</small></h3>
                            <p class="mb-0 fs-13 text-muted">Moyenne globale</p>
                        @else
                            <h3 class="mb-0 text-muted fw-bold">—</h3>
                            <p class="mb-0 fs-13 text-muted">Moyenne globale</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <span class="bgl-danger text-danger" style="width:44px;height:44px;display:flex;align-items:center;justify-content:center;border-radius:8px;flex-shrink:0;">
                        <i class="lni lni-comments-alt" style="font-size:20px;"></i>
                    </span>
                    <div>
                        <h3 class="mb-0 text-black fw-bold">{{ $commentaires->count() }}</h3>
                        <p class="mb-0 fs-13 text-muted">Commentaires</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($totalReponses === 0)
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="lni lni-bar-chart" style="font-size:48px;opacity:.3;display:block;margin-bottom:12px;"></i>
                Aucune évaluation reçue pour cet enseignant.
            </div>
        </div>
    @else

    <div class="row mb-4">
        {{-- Graphique par critère --}}
        <div class="col-xl-7 mb-4">
            <div class="card h-100">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-16 mb-0">Moyenne par critère</h4>
                </div>
                <div class="card-body">
                    @if($moyennesParCritere->isEmpty())
                        <p class="text-muted fs-13">Aucune donnée de critère disponible.</p>
                    @else
                        <canvas id="chartCriteres" style="max-height:280px;"></canvas>
                    @endif
                </div>
            </div>
        </div>

        {{-- Jauges critères --}}
        <div class="col-xl-5 mb-4">
            <div class="card h-100">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-16 mb-0">Détail critères</h4>
                </div>
                <div class="card-body">
                    @forelse($moyennesParCritere as $label => $moy)
                        @php $pct = round($moy / 5 * 100); @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fs-13">{{ $label }}</span>
                                <span class="fw-bold fs-13
                                    {{ $pct >= 80 ? 'text-success' : ($pct >= 60 ? 'text-warning' : 'text-danger') }}">
                                    {{ number_format($moy, 2) }}/5
                                </span>
                            </div>
                            <div class="progress" style="height:7px;">
                                <div class="progress-bar
                                    {{ $pct >= 80 ? 'bg-success' : ($pct >= 60 ? 'bg-warning' : 'bg-danger') }}"
                                    style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted fs-13">Aucun critère disponible.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau par questionnaire --}}
    <div class="card mb-4">
        <div class="card-header border-0 pb-0">
            <h4 class="fs-16 mb-0">Résultats par questionnaire</h4>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Titre / Matière</th>
                            <th>Classe</th>
                            <th>Annexe</th>
                            <th class="text-center">Réponses</th>
                            <th class="text-center">Moyenne</th>
                            <th>Statut</th>
                            <th>Créé le</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($statsParLien as $stat)
                        <tr>
                            <td class="ps-4 fw-semibold fs-14">{{ $stat['titre'] }}</td>
                            <td class="fs-13 text-muted">{{ $stat['classe'] }}</td>
                            <td class="fs-13 text-muted">{{ $stat['annexe'] }}</td>
                            <td class="text-center">
                                <span class="badge badge-sm badge-primary">{{ $stat['reponses'] }}</span>
                            </td>
                            <td class="text-center">
                                @if($stat['moyenne'] !== null)
                                    @php $p = round($stat['moyenne'] / 5 * 100); @endphp
                                    <span class="fw-bold {{ $p >= 80 ? 'text-success' : ($p >= 60 ? 'text-warning' : 'text-danger') }}">
                                        {{ number_format($stat['moyenne'], 2) }}/5
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($stat['statut'] === 'actif' && $stat['expire_at'] && $stat['expire_at']->isPast())
                                    <span class="badge badge-danger">Expiré</span>
                                @elseif($stat['statut'] === 'actif')
                                    <span class="badge badge-success">Actif</span>
                                @else
                                    <span class="badge badge-secondary">Fermé</span>
                                @endif
                            </td>
                            <td class="fs-13 text-muted">
                                {{ $stat['created_at'] ? $stat['created_at']->format('d/m/Y') : '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Commentaires --}}
    @if($commentaires->isNotEmpty())
    <div class="card">
        <div class="card-header border-0 pb-0">
            <h4 class="fs-16 mb-0">Commentaires reçus <small class="text-muted fs-13">({{ $commentaires->count() }} derniers)</small></h4>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($commentaires as $rep)
                <div class="col-md-6">
                    <div class="p-3 rounded" style="background:#f8f9fa;border-left:3px solid #2F4CDD;">
                        <p class="mb-1 fs-13" style="font-style:italic;">"{{ $rep->commentaire }}"</p>
                        @if($rep->soumis_at)
                            <small class="text-muted">{{ $rep->soumis_at->format('d/m/Y') }}</small>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @endif {{-- end if totalReponses --}}

</div>
@endsection

@push('scripts')
@if($totalReponses > 0 && $moyennesParCritere->isNotEmpty())
<script src="{{ asset('dashboard/vendor/chart.js/Chart.bundle.min.js') }}"></script>
<script>
(function () {
    const labels = {!! json_encode($moyennesParCritere->keys()->values()) !!};
    const data   = {!! json_encode($moyennesParCritere->values()->values()) !!};
    const colors = data.map(v => v >= 4 ? '#3ac977' : v >= 3 ? '#FF9F00' : '#FF5E5E');

    new Chart(document.getElementById('chartCriteres'), {
        type: 'horizontalBar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Moyenne /5',
                data: data,
                backgroundColor: colors,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { display: false },
            scales: {
                xAxes: [{ ticks: { min: 0, max: 5, stepSize: 1 }, gridLines: { color: '#f0f0f0' } }],
                yAxes: [{ ticks: { fontSize: 12 }, gridLines: { display: false } }]
            },
            tooltips: {
                callbacks: {
                    label: ctx => ' ' + ctx.xLabel.toFixed(2) + ' / 5'
                }
            }
        }
    });
})();
</script>
@endif
@endpush
