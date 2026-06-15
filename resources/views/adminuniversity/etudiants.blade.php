@extends('layouts.app')

@section('title', 'Étudiants')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Étudiants</h2>
            <p class="mb-0">Tous les étudiants inscrits, par annexe</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <span class="badge badge-primary px-3 py-2 fs-13">{{ $total }} étudiant{{ $total !== 1 ? 's' : '' }}</span>
            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalImportCSV">
                <i class="lni lni-upload me-1"></i>Importer CSV
            </button>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreerEtudiant">
                <i class="lni lni-plus me-1"></i>Ajouter
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filtres --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('adminuniversity.etudiants') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <input type="text" name="search" class="form-control w-auto"
                    placeholder="Nom, prénom ou email..."
                    value="{{ request('search') }}"
                    style="min-width:220px;">
                <select name="annexe_id" class="form-control w-auto">
                    <option value="">Toutes les annexes</option>
                    @foreach($annexes as $a)
                        <option value="{{ $a->id }}" {{ request('annexe_id') == $a->id ? 'selected' : '' }}>
                            {{ $a->nom }}{{ $a->ville ? ' — '.$a->ville : '' }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="lni lni-search-alt me-1"></i>Filtrer
                </button>
                @if(request()->hasAny(['search', 'annexe_id']))
                    <a href="{{ route('adminuniversity.etudiants') }}" class="btn btn-outline-secondary">
                        <i class="lni lni-close me-1"></i>Effacer
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Résultats groupés par annexe --}}
    @if($grouped->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="lni lni-graduation fs-1 d-block mb-3"></i>
                Aucun étudiant trouvé{{ request()->hasAny(['search', 'annexe_id']) ? ' pour ces critères.' : '.' }}
            </div>
        </div>
    @else
        @foreach($annexes as $annexe)
            @php $list = $grouped->get($annexe->id, collect()); @endphp
            @if($list->isEmpty()) @continue @endif

            <div class="card mb-3">
                <div class="card-header border-0 d-flex align-items-center justify-content-between py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="lni lni-map-marker text-primary"></i>
                        <h5 class="mb-0 font-w600">{{ $annexe->nom }}</h5>
                        @if($annexe->ville)
                            <span class="text-muted fs-13">— {{ $annexe->ville }}</span>
                        @endif
                    </div>
                    <span class="badge badge-primary">{{ $list->count() }} étudiant{{ $list->count() !== 1 ? 's' : '' }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Annexe</th>
                                    <th>Inscrit le</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($list as $etudiant)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold"
                                                style="width:32px;height:32px;flex-shrink:0;font-size:12px;background:#2F4CDD;">
                                                {{ strtoupper(substr($etudiant->prenom, 0, 1)) }}
                                            </div>
                                            <span class="font-w500">{{ $etudiant->prenom }} {{ $etudiant->nom }}</span>
                                        </div>
                                    </td>
                                    <td class="text-muted fs-13">{{ $etudiant->email }}</td>
                                    <td class="fs-13">
                                        @if($etudiant->annexe)
                                            <span class="badge badge-primary badge-sm">{{ $etudiant->annexe->nom }}</span>
                                            @if($etudiant->annexe->ville)
                                                <span class="text-muted"> — {{ $etudiant->annexe->ville }}</span>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-muted fs-13">{{ $etudiant->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <button class="btn btn-xs btn-outline-primary me-1"
                                            data-bs-toggle="modal" data-bs-target="#modalEditerEtudiant"
                                            data-id="{{ $etudiant->id }}"
                                            data-prenom="{{ $etudiant->prenom }}"
                                            data-nom="{{ $etudiant->nom }}"
                                            data-email="{{ $etudiant->email }}">
                                            <i class="lni lni-pencil"></i>
                                        </button>
                                        <button class="btn btn-xs btn-outline-danger"
                                            data-bs-toggle="modal" data-bs-target="#modalSupprimerEtudiant"
                                            data-id="{{ $etudiant->id }}"
                                            data-nom="{{ $etudiant->prenom }} {{ $etudiant->nom }}">
                                            <i class="lni lni-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Étudiants sans annexe (cas limite) --}}
        @php $sansAnnexe = $grouped->get('', collect())->merge($grouped->get(null, collect())); @endphp
        @if($sansAnnexe->isNotEmpty())
            <div class="card mb-3 border-warning">
                <div class="card-header border-0 d-flex align-items-center justify-content-between py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="lni lni-warning text-warning"></i>
                        <h5 class="mb-0 font-w600">Sans annexe</h5>
                    </div>
                    <span class="badge badge-warning">{{ $sansAnnexe->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr><th>Nom</th><th>Email</th><th>Inscrit le</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                @foreach($sansAnnexe as $etudiant)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold"
                                                style="width:32px;height:32px;flex-shrink:0;font-size:12px;background:#2F4CDD;">
                                                {{ strtoupper(substr($etudiant->prenom, 0, 1)) }}
                                            </div>
                                            <span class="font-w500">{{ $etudiant->prenom }} {{ $etudiant->nom }}</span>
                                        </div>
                                    </td>
                                    <td class="text-muted fs-13">{{ $etudiant->email }}</td>
                                    <td class="text-muted fs-13 text-warning">Non assigné</td>
                                    <td class="text-muted fs-13">{{ $etudiant->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <button class="btn btn-xs btn-outline-primary me-1"
                                            data-bs-toggle="modal" data-bs-target="#modalEditerEtudiant"
                                            data-id="{{ $etudiant->id }}"
                                            data-prenom="{{ $etudiant->prenom }}"
                                            data-nom="{{ $etudiant->nom }}"
                                            data-email="{{ $etudiant->email }}">
                                            <i class="lni lni-pencil"></i>
                                        </button>
                                        <button class="btn btn-xs btn-outline-danger"
                                            data-bs-toggle="modal" data-bs-target="#modalSupprimerEtudiant"
                                            data-id="{{ $etudiant->id }}"
                                            data-nom="{{ $etudiant->prenom }} {{ $etudiant->nom }}">
                                            <i class="lni lni-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endif

</div>

{{-- Modal : Créer étudiant --}}
<div class="modal fade" id="modalCreerEtudiant" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un étudiant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('adminuniversity.etudiants.creer') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="prenom" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email institutionnel <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="prenom.nom@etud.univ.fr" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Annexe</label>
                            <select name="annexe_id" class="form-control">
                                <option value="">— Sélectionner une annexe —</option>
                                @foreach($annexes as $a)
                                    <option value="{{ $a->id }}">{{ $a->nom }}{{ $a->ville ? ' — '.$a->ville : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="lni lni-plus me-1"></i>Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal : Éditer étudiant --}}
<div class="modal fade" id="modalEditerEtudiant" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier l'étudiant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditerEtudiant" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prénom</label>
                            <input type="text" name="prenom" id="editPrenom" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom</label>
                            <input type="text" name="nom" id="editNom" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" id="editEmail" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="lni lni-save me-1"></i>Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal : Supprimer --}}
<div class="modal fade" id="modalSupprimerEtudiant" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Supprimer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formSupprimerEtudiant" method="POST">
                @csrf @method('DELETE')
                <div class="modal-body">
                    <p>Supprimer <strong id="nomSupprimerEtudiant"></strong> ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger btn-sm"><i class="lni lni-trash me-1"></i>Supprimer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal : Import CSV --}}
<div class="modal fade" id="modalImportCSV" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Importer des étudiants via CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('adminuniversity.etudiants.importer') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info fs-13">
                        <strong>Format attendu :</strong> prénom, nom, email, annexe_id<br>
                        La première ligne (en-têtes) sera ignorée.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fichier CSV <span class="text-danger">*</span></label>
                        <input type="file" name="fichier_csv" class="form-control" accept=".csv,.txt" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="lni lni-upload me-1"></i>Importer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('click', function (e) {
        const editBtn = e.target.closest('[data-bs-target="#modalEditerEtudiant"]');
        if (editBtn) {
            document.getElementById('editPrenom').value = editBtn.dataset.prenom;
            document.getElementById('editNom').value    = editBtn.dataset.nom;
            document.getElementById('editEmail').value  = editBtn.dataset.email;
            document.getElementById('formEditerEtudiant').action = '/adminuniversity/etudiants/' + editBtn.dataset.id;
        }

        const delBtn = e.target.closest('[data-bs-target="#modalSupprimerEtudiant"]');
        if (delBtn) {
            document.getElementById('nomSupprimerEtudiant').textContent = delBtn.dataset.nom;
            document.getElementById('formSupprimerEtudiant').action = '/adminuniversity/etudiants/' + delBtn.dataset.id;
        }
    });
</script>
@endpush
