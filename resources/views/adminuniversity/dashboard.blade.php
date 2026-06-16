@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Tableau de bord</h2>
            <p class="mb-0">Vue d'ensemble de votre université</p>
        </div>
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
                <div class="card-body p-4 d-flex flex-column">
                    <span class="bgl-primary text-primary mb-3" style="width:fit-content;padding:8px;border-radius:6px;">
                        <i class="lni lni-graduation" style="font-size:14px;"></i>
                    </span>
                    <div>
                        <h3 class="mb-0 text-black"><span class="counter ms-0">{{ $nbEnseignants }}</span></h3>
                        <p class="mb-0 fs-13">Enseignants</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4 d-flex flex-column">
                    <span class="bgl-success text-success mb-3" style="width:fit-content;padding:8px;border-radius:6px;">
                        <i class="lni lni-home" style="font-size:14px;"></i>
                    </span>
                    <div>
                        <h3 class="mb-0 text-black"><span class="counter ms-0">{{ $nbAnnexes }}</span></h3>
                        <p class="mb-0 fs-13">Annexes</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4 d-flex flex-column">
                    <span class="bgl-warning text-warning mb-3" style="width:fit-content;padding:8px;border-radius:6px;">
                        <i class="lni lni-star" style="font-size:14px;"></i>
                    </span>
                    <div>
                        <h3 class="mb-0 text-black"><span class="counter ms-0">{{ $nbEvaluations }}</span></h3>
                        <p class="mb-0 fs-13">Évaluations soumises</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4 d-flex flex-column">
                    <span class="bgl-danger text-danger mb-3" style="width:fit-content;padding:8px;border-radius:6px;">
                        <i class="lni lni-files" style="font-size:14px;"></i>
                    </span>
                    <div>
                        <h3 class="mb-0 text-black"><span class="counter ms-0">{{ $nbLiens }}</span></h3>
                        <p class="mb-0 fs-13">Questionnaires créés</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Satisfaction moyenne par annexe --}}
        <div class="col-xl-8 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Satisfaction moyenne par annexe</h4>
                    <small class="text-muted">Score global des évaluations reçues (%)</small>
                </div>
                <div class="card-body">
                    @if($statsParAnnexe->every(fn($s) => $s['count'] === 0))
                        <p class="text-muted text-center py-4">Aucune évaluation reçue pour le moment.</p>
                    @else
                        <canvas id="chartAnnexes" height="120"></canvas>
                    @endif
                </div>
            </div>
        </div>

        {{-- Enseignants récents --}}
        <div class="col-xl-4 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0 d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Enseignants récents</h4>
                    <a href="{{ route('adminuniversity.enseignants') }}" class="btn btn-xs btn-primary">Voir tous</a>
                </div>
                <div class="card-body p-0">
                    @forelse($enseignantsRecents as $ens)
                    <div class="d-flex align-items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold flex-shrink-0"
                            style="width:36px;height:36px;font-size:13px;background:#2BC155;">
                            {{ strtoupper(substr($ens->prenom, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="font-w500 fs-14 text-truncate">{{ $ens->prenom }} {{ $ens->nom }}</div>
                            <div class="fs-12 text-muted text-truncate">
                                @foreach($ens->annexes->take(2) as $a)
                                    <span>{{ $a->nom }}</span>{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </div>
                        </div>
                        <small class="text-muted fs-11 flex-shrink-0">{{ $ens->created_at->format('d/m') }}</small>
                    </div>
                    @empty
                    <p class="text-muted text-center py-4 fs-13">Aucun enseignant inscrit.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Accès rapide --}}
    <div class="row">
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('adminuniversity.enseignants') }}" class="card text-decoration-none">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded" style="width:48px;height:48px;">
                        <i class="flaticon-381-television fs-20"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Gérer les enseignants</h5>
                        <small class="text-muted">Liste complète</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('adminuniversity.annexes') }}" class="card text-decoration-none">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="d-flex align-items-center justify-content-center bg-success text-white rounded" style="width:48px;height:48px;">
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
            <a href="{{ route('adminuniversity.questionnaires') }}" class="card text-decoration-none">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="d-flex align-items-center justify-content-center bg-warning text-white rounded" style="width:48px;height:48px;">
                        <i class="flaticon-381-notepad fs-20"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Configurer les critères</h5>
                        <small class="text-muted">Critères d'évaluation</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('adminuniversity.rapports') }}" class="card text-decoration-none">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="d-flex align-items-center justify-content-center bg-info text-white rounded" style="width:48px;height:48px;">
                        <i class="flaticon-381-internet fs-20"></i>
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

    @if($statsParAnnexe->some(fn($s) => $s['count'] > 0))
    new Chart(document.getElementById('chartAnnexes'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($statsParAnnexe->pluck('nom')) !!},
            datasets: [{
                label: 'Satisfaction (%)',
                data: {!! json_encode($statsParAnnexe->pluck('moyenne')) !!},
                backgroundColor: '#2F4CDD',
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } } }
        }
    });
    @endif
</script>
@endpush
