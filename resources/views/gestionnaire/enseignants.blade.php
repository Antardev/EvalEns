@extends('layouts.app')

@section('title', 'Enseignants — ' . $annexe->nom)

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Enseignants</h2>
            <p class="mb-0">{{ $annexe->nom }} — {{ $annexe->ville ?? '' }}</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <span class="badge badge-success px-3 py-2 fs-13">{{ $total }} enseignant{{ $total !== 1 ? 's' : '' }}</span>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalImportEnseignants">
                <i class="lni lni-plus me-1"></i>Importer Excel
            </button>
        </div>
    </div>

    <div class="modal fade" id="modalImportEnseignants" tabindex="-1" aria-labelledby="modalImportEnseignantsLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('gestionnaire.enseignants.importer') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalImportEnseignantsLabel">Importer des enseignants depuis Excel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">

                        @error('fichier')
                            <div class="alert alert-danger py-2 fs-13">{{ $message }}</div>
                        @enderror

                        <div class="mb-3">
                            <label class="form-label">Fichier Excel</label>
                            <input type="file" name="fichier" class="form-control @error('fichier') is-invalid @enderror" accept=".xlsx,.xls,.csv" required>
                            <small class="text-muted">Le fichier doit contenir au moins les colonnes Prénom, Nom et Email.</small>
                        </div>
                        <div class="mb-3">
                            <a href="{{ asset('models/modele_enseignants.xlsx') }}" class="btn btn-outline-secondary btn-sm" download>
                                <i class="lni lni-download me-1"></i>Télécharger le modèle Excel
                            </a>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Importer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Messages de résultat de l'import --}}
    @if(session('success'))
        <div class="alert alert-success mb-3">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger mb-3">
            {{ session('error') }}
        </div>
    @endif

    @if(session('import_errors') && count(session('import_errors')) > 0)
        <div class="alert alert-warning mb-3">
            <strong>{{ count(session('import_errors')) }} ligne(s) ignorée(s) :</strong>
            <ul class="mb-0 mt-2 fs-13">
                @foreach(session('import_errors') as $erreur)
                    <li>{{ $erreur }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Recherche --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('gestionnaire.enseignants') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <input type="text" name="search" class="form-control w-auto"
                    placeholder="Rechercher un enseignant..."
                    value="{{ request('search') }}"
                    style="min-width:220px;">
                <button type="submit" class="btn btn-primary">
                    <i class="lni lni-search-alt me-1"></i>Rechercher
                </button>
                @if(request('search'))
                    <a href="{{ route('gestionnaire.enseignants') }}" class="btn btn-outline-secondary">
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
                    <i class="lni lni-blackboard fs-1 d-block mb-3"></i>
                    Aucun enseignant trouvé{{ request('search') ? ' pour cette recherche' : ' dans cette annexe' }}.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Ajouté le</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($membres as $m)
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
                                <td class="text-muted fs-13">{{ $m->created_at->format('d/m/Y') }}</td>
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
                        {{ $membres->firstItem() }}–{{ $membres->lastItem() }} sur {{ $membres->total() }} enseignants
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

@if($errors->any() && $errors->has('fichier'))
<script>
    // Rouvre automatiquement la modale si la validation du fichier a échoué
    document.addEventListener('DOMContentLoaded', function () {
        var modal = new bootstrap.Modal(document.getElementById('modalImportEnseignants'));
        modal.show();
    });
</script>
@endif
