@extends('layouts.app')

@section('title', 'Enseignants questionnés — ' . $annexe->nom)

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Enseignants questionnés</h2>
            <p class="mb-0">{{ $annexe->nom }} — enseignants ayant déjà reçu au moins une évaluation</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <span class="badge badge-success px-3 py-2 fs-13">{{ $total }} enseignant{{ $total !== 1 ? 's' : '' }}</span>
            <a href="{{ route('gestionnaire.enseignants') }}" class="btn btn-outline-secondary btn-sm">
                <i class="lni lni-eye me-1"></i>Voir tous
            </a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('gestionnaire.enseignants.questionnes') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <input type="text" name="search" class="form-control w-auto"
                    placeholder="Rechercher un enseignant questionné..."
                    value="{{ request('search') }}"
                    style="min-width:220px;">
                <button type="submit" class="btn btn-primary">
                    <i class="lni lni-search-alt me-1"></i>Rechercher
                </button>
                @if(request('search'))
                    <a href="{{ route('gestionnaire.enseignants.questionnes') }}" class="btn btn-outline-secondary">
                        <i class="lni lni-close me-1"></i>Effacer
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($membres->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="lni lni-user fs-1 d-block mb-3"></i>
                    Aucun enseignant n'a encore reçu d'évaluation{{ request('search') ? ' pour cette recherche' : '' }}.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th class="text-center">Questionnaires</th>
                                <th class="text-center">Réponses</th>
                                <th class="text-center">Dernière évaluation</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($membres as $m)
                                @php
                                    $lastEvaluation = $m->liensQuestionnaires
                                        ->flatMap(fn($lien) => $lien->reponses)
                                        ->sortByDesc('soumis_at')
                                        ->first();
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold"
                                                style="width:36px;height:36px;flex-shrink:0;font-size:13px;background:#2BC155;">
                                                {{ strtoupper(substr($m->prenom ?: $m->nom ?: '?', 0, 1)) }}
                                            </div>
                                            <div class="font-w500">{{ $m->prenom }} {{ $m->nom }}</div>
                                        </div>
                                    </td>
                                    <td class="text-muted fs-13">{{ $m->email }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $m->questionnaires_count ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">{{ $m->evaluations_count ?? 0 }}</span>
                                    </td>
                                    <td class="text-center text-muted fs-13">
                                        {{ $lastEvaluation && $lastEvaluation->soumis_at ? $lastEvaluation->soumis_at->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('gestionnaire.enseignants.statistiques', $m->id) }}"
                                           class="btn btn-sm btn-outline-primary me-1"
                                           data-bs-toggle="tooltip" data-bs-placement="top" title="Statistiques">
                                            <i class="lni lni-bar-chart"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            title="Supprimer"
                                            data-bs-toggle="modal" data-bs-target="#modalSupprimerEnseignant"
                                            data-id="{{ $m->id }}"
                                            data-nom="{{ $m->prenom }} {{ $m->nom }}">
                                            <i class="lni lni-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span class="text-muted fs-13">
                        {{ $membres->firstItem() }}–{{ $membres->lastItem() }} sur {{ $membres->total() }} enseignants questionnés
                    </span>
                    {{ $membres->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="modalSupprimerEnseignant" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">Supprimer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formSupprimerEnseignant" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-body">
                        <p>Supprimer <strong id="nomSupprimerEnseignant"></strong> de cette annexe ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger btn-sm"><i class="lni lni-trash me-1"></i>Supprimer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    });

    document.addEventListener('click', function (e) {
        const delBtn = e.target.closest('[data-bs-target="#modalSupprimerEnseignant"]');
        if (delBtn) {
            document.getElementById('nomSupprimerEnseignant').textContent = delBtn.dataset.nom;
            document.getElementById('formSupprimerEnseignant').action = '/gestionnaire/enseignants/' + delBtn.dataset.id;
        }
    });
</script>
@endpush
