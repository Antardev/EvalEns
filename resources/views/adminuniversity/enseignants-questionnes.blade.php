@extends('layouts.app')

@section('title', 'Enseignants questionnés')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Enseignants questionnés</h2>
            <p class="mb-0">Enseignants ayant déjà reçu au moins une évaluation</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <span class="badge badge-success px-3 py-2 fs-13">{{ $total }} enseignant{{ $total !== 1 ? 's' : '' }}</span>
            <a href="{{ route('adminuniversity.enseignants') }}" class="btn btn-outline-secondary btn-sm">
                <i class="lni lni-eye me-1"></i>Voir tous
            </a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('adminuniversity.enseignants.questionnes') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <input type="text" name="search" class="form-control w-auto"
                    placeholder="Rechercher un enseignant questionné..."
                    value="{{ request('search') }}"
                    style="min-width:220px;">
                <button type="submit" class="btn btn-primary">
                    <i class="lni lni-search-alt me-1"></i>Rechercher
                </button>
                @if(request('search'))
                    <a href="{{ route('adminuniversity.enseignants.questionnes') }}" class="btn btn-outline-secondary">
                        <i class="lni lni-close me-1"></i>Effacer
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($enseignants->isEmpty())
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
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enseignants as $enseignant)
                                @php
                                    $lastEvaluation = $enseignant->liensQuestionnaires
                                        ->flatMap(fn($lien) => $lien->reponses)
                                        ->sortByDesc('soumis_at')
                                        ->first();
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold"
                                                style="width:36px;height:36px;flex-shrink:0;font-size:13px;background:#2BC155;">
                                                {{ strtoupper(substr($enseignant->prenom ?: $enseignant->nom ?: '?', 0, 1)) }}
                                            </div>
                                            <div class="font-w500">{{ $enseignant->prenom }} {{ $enseignant->nom }}</div>
                                        </div>
                                    </td>
                                    <td class="text-muted fs-13">{{ $enseignant->email }}</td>
                                    <td class="text-center"><span class="badge badge-info">{{ $enseignant->questionnaires_count ?? 0 }}</span></td>
                                    <td class="text-center"><span class="badge badge-primary">{{ $enseignant->evaluations_count ?? 0 }}</span></td>
                                    <td class="text-center text-muted fs-13">
                                        {{ $lastEvaluation && $lastEvaluation->soumis_at ? $lastEvaluation->soumis_at->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('adminuniversity.enseignants.statistiques', $enseignant->id) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           data-bs-toggle="tooltip" data-bs-placement="top" title="Statistiques">
                                            <i class="lni lni-bar-chart"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span class="text-muted fs-13">
                        {{ $enseignants->firstItem() }}–{{ $enseignants->lastItem() }} sur {{ $enseignants->total() }} enseignants questionnés
                    </span>
                    {{ $enseignants->links() }}
                </div>
            @endif
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
</script>
@endpush
