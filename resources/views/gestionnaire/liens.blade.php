@extends('layouts.app')

@section('title', 'Liens questionnaires')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-4 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Liens questionnaires</h2>
            <p class="mb-0">{{ $annexe->nom }} — Générez des liens à partager avec vos classes</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreer">
            <i class="lni lni-plus me-1"></i>Nouveau lien
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="lni lni-checkmark-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="lni lni-warning me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($liens->isEmpty())
        <div class="card border-0">
            <div class="card-body text-center py-5">
                <i class="lni lni-link fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">Aucun lien questionnaire créé</h5>
                <p class="text-muted fs-13">Créez un lien pour une classe et partagez-le avec vos étudiants.</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreer">
                    <i class="lni lni-plus me-1"></i>Créer le premier lien
                </button>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($liens as $lien)
            @php
                $isActif   = $lien->isActif();
                $isExpire  = $lien->expire_at && $lien->expire_at->isPast();
                $url       = $lien->urlPublique();
            @endphp
            <div class="col-xl-6 col-12">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="flex-grow-1 me-3">
                                <h6 class="font-w600 mb-1">{{ $lien->titre }}</h6>
                                <div class="d-flex flex-wrap gap-2 mb-1">
                                    <span class="badge badge-primary">{{ $lien->classe }}</span>
                                    @if($lien->matiere)
                                        <span class="badge badge-info">{{ $lien->matiere }}</span>
                                    @endif
                                    @if($lien->enseignant)
                                        <span class="badge badge-success">{{ $lien->enseignant->prenom }} {{ $lien->enseignant->nom }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($isActif)
                                <span class="badge badge-success px-2 py-1 fs-12">Actif</span>
                            @elseif($isExpire)
                                <span class="badge badge-danger px-2 py-1 fs-12">Expiré</span>
                            @elseif($lien->statut === 'ferme')
                                <span class="badge badge-secondary px-2 py-1 fs-12">Fermé</span>
                            @endif
                        </div>

                        {{-- Stats réponses --}}
                        <div class="d-flex gap-3 mb-3">
                            <span class="text-muted fs-13">
                                <i class="lni lni-checkmark-circle me-1 text-success"></i>
                                <strong>{{ $lien->reponses_count }}</strong> réponse(s)
                            </span>
                            @if($lien->expire_at)
                                <span class="fs-13 {{ $isExpire ? 'text-danger' : 'text-muted' }}">
                                    <i class="lni lni-calendar me-1"></i>
                                    {{ $isExpire ? 'Expiré le' : 'Expire le' }} {{ $lien->expire_at->format('d/m/Y à H:i') }}
                                </span>
                            @endif
                            <span class="text-muted fs-13">
                                <i class="lni lni-question-circle me-1"></i>
                                {{ count($lien->questions) }} critère(s)
                            </span>
                        </div>

                        {{-- URL copiable --}}
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" class="form-control fs-12 bg-light" id="url-{{ $lien->id }}"
                                value="{{ $url }}" readonly>
                            <button class="btn btn-outline-secondary" type="button"
                                onclick="copierUrl('url-{{ $lien->id }}', this)" title="Copier le lien">
                                <i class="lni lni-files"></i>
                            </button>
                            @if($isActif)
                                <a href="{{ $url }}" target="_blank" class="btn btn-outline-primary" title="Ouvrir">
                                    <i class="lni lni-exit"></i>
                                </a>
                            @endif
                        </div>

                                        {{-- Alerte snapshot désynchronisé --}}
                        @if($lien->reponses_count === 0 && count($lien->questions) < $criteres->count())
                        <div class="alert alert-warning py-2 fs-12 mb-3 d-flex align-items-center justify-content-between">
                            <span>
                                <i class="lni lni-warning me-1"></i>
                                Ce lien a <strong>{{ count($lien->questions) }} critère(s)</strong> — les critères actuels en ont <strong>{{ $criteres->count() }}</strong>.
                            </span>
                            <form method="POST" action="{{ route('gestionnaire.liens.rafraichir', $lien->id) }}" class="ms-2">
                                @csrf
                                <button type="submit" class="btn btn-xs btn-warning">
                                    <i class="lni lni-reload me-1"></i>Rafraîchir
                                </button>
                            </form>
                        </div>
                        @endif

                        {{-- Actions --}}
                        <div class="d-flex gap-2 flex-wrap">
                            @if($lien->reponses_count > 0)
                                <a href="{{ route('gestionnaire.liens.reponses', $lien->id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="lni lni-bar-chart me-1"></i>Voir les réponses
                                </a>
                            @endif

                            @if($isExpire)
                                <span class="btn btn-sm btn-danger disabled" style="cursor:default;">
                                    <i class="lni lni-timer me-1"></i>Expiré le {{ $lien->expire_at->format('d/m/Y à H:i') }}
                                </span>
                            @else
                                <form method="POST" action="{{ route('gestionnaire.liens.fermer', $lien->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $isActif ? 'btn-warning' : 'btn-success' }}">
                                        <i class="lni lni-{{ $isActif ? 'lock' : 'unlock' }} me-1"></i>
                                        {{ $isActif ? 'Fermer' : 'Rouvrir' }}
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('gestionnaire.liens.supprimer', $lien->id) }}"
                                onsubmit="return confirm('Supprimer ce lien et toutes ses réponses ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="lni lni-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-footer border-0 bg-transparent px-4 py-2">
                        <small class="text-muted fs-11">Créé le {{ $lien->created_at->format('d/m/Y à H:i') }}</small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>

{{-- Modal création --}}
<div class="modal fade" id="modalCreer" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title font-w600">Nouveau lien questionnaire</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('gestionnaire.liens.creer') }}">
                @csrf
                <div class="modal-body">

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Titre du questionnaire <span class="text-danger">*</span></label>
                            <input type="text" name="titre" class="form-control"
                                value="{{ old('titre') }}"
                                placeholder="Ex: Évaluation des enseignements — S1 2025-2026" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Classe <span class="text-danger">*</span></label>
                            <input type="text" name="classe" class="form-control"
                                value="{{ old('classe') }}"
                                placeholder="Ex: L3 Informatique" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Matière </label>
                            <input type="text" name="matiere" class="form-control"
                                value="{{ old('matiere') }}"
                                placeholder="Ex: Algorithmique avancée">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Enseignant évalué </label>
                            <select name="enseignant_id" class="form-select">
                                <option value="">— Aucun / Général —</option>
                                @foreach($enseignants as $ens)
                                    <option value="{{ $ens->id }}" {{ old('enseignant_id') == $ens->id ? 'selected' : '' }}>
                                        {{ $ens->prenom }} {{ $ens->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date d'expiration </label>
                            <input type="datetime-local" name="expire_at" class="form-control"
                                value="{{ old('expire_at') }}">
                        </div>

                        {{-- Aperçu des critères --}}
                        @if($criteres->isNotEmpty())
                        <div class="col-12">
                            <label class="form-label fw-semibold">Critères inclus dans ce questionnaire</label>
                            <div class="bg-light rounded p-3">
                                @foreach($criteres as $c)
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge badge-primary">{{ $c->poids }}%</span>
                                        <span class="fs-13">{{ $c->nom }}</span>
                                    </div>
                                @endforeach
                                <p class="text-muted fs-12 mb-0 mt-2">
                                    Ces critères sont configurés par votre administrateur université.
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="lni lni-link me-1"></i>Générer le lien
                    </button>
                </div>
            </form>
        </div>
    </div>
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

// Ouvrir le modal si erreurs de validation
@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal(document.getElementById('modalCreer')).show();
    });
@endif
</script>
@endpush
