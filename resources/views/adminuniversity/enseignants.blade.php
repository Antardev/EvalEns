@extends('layouts.app')

@section('title', 'Enseignants')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Enseignants</h2>
            <p class="mb-0">Tous les enseignants inscrits dans cette université</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <span class="badge badge-success px-3 py-2 fs-13">{{ $total }} enseignant{{ $total !== 1 ? 's' : '' }}</span>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreerEnseignant">
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
            <form method="GET" action="{{ route('adminuniversity.enseignants') }}" class="d-flex flex-wrap gap-2 align-items-center">
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
                    <a href="{{ route('adminuniversity.enseignants') }}" class="btn btn-outline-secondary">
                        <i class="lni lni-close me-1"></i>Effacer
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Résultats --}}
    @if($enseignants->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="lni lni-blackboard fs-1 d-block mb-3"></i>
                Aucun enseignant trouvé{{ request()->hasAny(['search', 'annexe_id']) ? ' pour ces critères.' : '.' }}
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Annexes</th>
                                <th>Inscrit le</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enseignants as $enseignant)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold"
                                            style="width:32px;height:32px;flex-shrink:0;font-size:12px;background:#2BC155;">
                                            {{ strtoupper(substr($enseignant->prenom, 0, 1)) }}
                                        </div>
                                        <span class="font-w500">{{ $enseignant->prenom }} {{ $enseignant->nom }}</span>
                                    </div>
                                </td>
                                <td class="text-muted fs-13">{{ $enseignant->email }}</td>
                                <td class="fs-13">
                                    @forelse($enseignant->annexes as $annexe)
                                        <span class="badge badge-success me-1 mb-1">
                                            <i class="lni lni-map-marker me-1"></i>{{ $annexe->nom }}
                                            @if($annexe->ville)
                                                <span class="text-white-50">— {{ $annexe->ville }}</span>
                                            @endif
                                        </span>
                                    @empty
                                        <span class="text-muted">—</span>
                                    @endforelse
                                </td>
                                <td class="text-muted fs-13">{{ $enseignant->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('adminuniversity.enseignants.statistiques', $enseignant->id) }}"
                                        class="btn btn-xs btn-outline-info me-1" title="Statistiques d'évaluation">
                                        <i class="lni lni-bar-chart"></i>
                                    </a>
                                    <button class="btn btn-xs btn-outline-primary me-1"
                                        data-bs-toggle="modal" data-bs-target="#modalEditerEnseignant"
                                        data-id="{{ $enseignant->id }}"
                                        data-prenom="{{ $enseignant->prenom }}"
                                        data-nom="{{ $enseignant->nom }}"
                                        data-email="{{ $enseignant->email }}">
                                        <i class="lni lni-pencil"></i>
                                    </button>
                                    <button class="btn btn-xs btn-outline-danger"
                                        data-bs-toggle="modal" data-bs-target="#modalSupprimerEnseignant"
                                        data-id="{{ $enseignant->id }}"
                                        data-nom="{{ $enseignant->prenom }} {{ $enseignant->nom }}">
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

        <div class="mt-3 d-flex justify-content-center">
            {{ $enseignants->links() }}
        </div>
    @endif

</div>

{{-- Modal : Créer --}}
<div class="modal fade" id="modalCreerEnseignant" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un enseignant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('adminuniversity.enseignants.creer') }}">
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
                            <input type="email" name="email" class="form-control" placeholder="prenom.nom@univ.fr" required>
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

{{-- Modal : Éditer --}}
<div class="modal fade" id="modalEditerEnseignant" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier l'enseignant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditerEnseignant" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prénom</label>
                            <input type="text" name="prenom" id="eEditPrenom" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom</label>
                            <input type="text" name="nom" id="eEditNom" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" id="eEditEmail" class="form-control" required>
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
                    <p>Supprimer <strong id="nomSupprimerEnseignant"></strong> ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger btn-sm"><i class="lni lni-trash me-1"></i>Supprimer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('click', function (e) {
        const editBtn = e.target.closest('[data-bs-target="#modalEditerEnseignant"]');
        if (editBtn) {
            document.getElementById('eEditPrenom').value = editBtn.dataset.prenom;
            document.getElementById('eEditNom').value    = editBtn.dataset.nom;
            document.getElementById('eEditEmail').value  = editBtn.dataset.email;
            document.getElementById('formEditerEnseignant').action = '/adminuniversity/enseignants/' + editBtn.dataset.id;
        }

        const delBtn = e.target.closest('[data-bs-target="#modalSupprimerEnseignant"]');
        if (delBtn) {
            document.getElementById('nomSupprimerEnseignant').textContent = delBtn.dataset.nom;
            document.getElementById('formSupprimerEnseignant').action = '/adminuniversity/enseignants/' + delBtn.dataset.id;
        }
    });
</script>
@endpush
