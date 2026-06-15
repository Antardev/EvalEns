@extends('layouts.app')

@section('title', 'Réponses — ' . $lien->titre)

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-4 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Réponses reçues</h2>
            <p class="mb-0">{{ $lien->titre }} — {{ $lien->classe }}</p>
        </div>
        <a href="{{ route('gestionnaire.liens') }}" class="btn btn-outline-primary">
            <i class="lni lni-arrow-left me-1"></i>Retour aux liens
        </a>
    </div>

    {{-- Résumé --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-1 font-w700 text-primary">{{ $reponses->count() }}</div>
                <div class="text-muted fs-13">Réponses reçues</div>
            </div>
        </div>
        @if($reponses->isNotEmpty())
            @php $moyGlobale = round($reponses->avg(fn($r) => $r->moyenneGlobale()), 2); @endphp
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fs-1 font-w700 text-success">{{ number_format($moyGlobale, 1) }}/5</div>
                    <div class="text-muted fs-13">Moyenne globale</div>
                </div>
            </div>
        @endif
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-1 font-w700 text-info">{{ $lien->isActif() ? 'Actif' : 'Fermé' }}</div>
                <div class="text-muted fs-13">Statut du lien</div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-1 font-w700 text-warning">{{ count($lien->questions) }}</div>
                <div class="text-muted fs-13">Critères évalués</div>
            </div>
        </div>
    </div>

    @if($reponses->isEmpty())
        <div class="card border-0">
            <div class="card-body text-center py-5">
                <i class="lni lni-inbox fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">Aucune réponse pour l'instant</h5>
                @if($lien->isActif())
                    <p class="text-muted fs-13">Partagez le lien avec vos étudiants :</p>
                    <div class="input-group mx-auto" style="max-width:500px;">
                        <input type="text" class="form-control fs-12 bg-light" id="urlReponse"
                            value="{{ $lien->urlPublique() }}" readonly>
                        <button class="btn btn-outline-secondary" onclick="copierUrl('urlReponse', this)">
                            <i class="lni lni-files"></i>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @else

        {{-- Moyennes par critère --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header border-0 pb-0">
                <h5 class="card-title mb-0">Moyennes par critère</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($moyennes as $label => $moy)
                    <div class="col-xl-4 col-md-6">
                        <div class="bg-light rounded p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fs-13 font-w500">{{ $label }}</span>
                                <span class="badge {{ $moy >= 4 ? 'badge-success' : ($moy >= 3 ? 'badge-primary' : ($moy >= 2 ? 'badge-warning' : 'badge-danger')) }} fs-13">
                                    {{ $moy !== null ? number_format($moy, 1) . '/5' : '—' }}
                                </span>
                            </div>
                            @if($moy !== null)
                                <div class="progress" style="height:6px;">
                                    <div class="progress-bar {{ $moy >= 4 ? 'bg-success' : ($moy >= 3 ? 'bg-primary' : ($moy >= 2 ? 'bg-warning' : 'bg-danger')) }}"
                                        style="width:{{ ($moy / 5) * 100 }}%"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Tableau des réponses --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Détail des réponses</h5>
                <small class="text-muted">{{ $reponses->count() }} réponse(s)</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                @foreach($lien->questions as $q)
                                    <th class="text-center">{{ $q['label'] }}</th>
                                @endforeach
                                <th class="text-center">Moy.</th>
                                <th>Commentaire</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reponses as $i => $reponse)
                            <tr>
                                <td class="ps-4 text-muted fs-13">{{ $i + 1 }}</td>
                                @foreach($lien->questions as $q)
                                    @php
                                        $score = collect($reponse->scores)->firstWhere('label', $q['label'])['score'] ?? null;
                                        $cls   = $score ? match(true) {
                                            $score >= 5 => 'text-success',
                                            $score >= 4 => 'text-primary',
                                            $score >= 3 => 'text-info',
                                            $score >= 2 => 'text-warning',
                                            default     => 'text-danger',
                                        } : '';
                                    @endphp
                                    <td class="text-center">
                                        <span class="font-w700 {{ $cls }}">{{ $score ?? '—' }}</span>
                                    </td>
                                @endforeach
                                <td class="text-center">
                                    <strong>{{ number_format($reponse->moyenneGlobale(), 1) }}</strong>
                                </td>
                                <td class="fs-12" style="max-width:200px;">
                                    @if($reponse->commentaire)
                                        <span class="text-truncate d-block" title="{{ $reponse->commentaire }}"
                                            style="max-width:180px;">
                                            {{ Str::limit($reponse->commentaire, 60) }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-muted fs-12">
                                    {{ $reponse->soumis_at->format('d/m H:i') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Commentaires --}}
        @php $avecCommentaires = $reponses->filter(fn($r) => $r->commentaire); @endphp
        @if($avecCommentaires->isNotEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 pb-0">
                <h5 class="card-title mb-0">Commentaires libres ({{ $avecCommentaires->count() }})</h5>
            </div>
            <div class="card-body">
                @foreach($avecCommentaires as $reponse)
                    <div class="bg-light rounded p-3 mb-2">
                        <p class="mb-1 fs-13">{{ $reponse->commentaire }}</p>
                        <small class="text-muted">{{ $reponse->soumis_at->format('d/m/Y à H:i') }}</small>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    @endif

</div>
@endsection

@push('scripts')
<script>
function copierUrl(inputId, btn) {
    const input = document.getElementById(inputId);
    navigator.clipboard.writeText(input.value).then(() => {
        const icon = btn.querySelector('i');
        icon.className = 'lni lni-checkmark text-success';
        setTimeout(() => icon.className = 'lni lni-files', 2000);
    });
}
</script>
@endpush
