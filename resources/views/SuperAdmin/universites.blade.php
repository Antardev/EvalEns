@extends('layouts.superadmin')

@section('title', 'Référentiel des universités')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Référentiel des universités</h2>
            <p class="mb-0">Liste des établissements disponibles pour l'inscription des directeurs</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreerUniversite">
            <i class="lni lni-plus me-1"></i> Ajouter une université
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Barre de recherche --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <input type="text" class="form-control w-auto" id="searchUniversites"
                    placeholder="Rechercher par nom ou acronyme…" style="min-width:260px;">
                <span class="text-muted fs-13 ms-auto" id="compteur">
                    {{ $references->count() }} université{{ $references->count() !== 1 ? 's' : '' }}
                </span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tableUniversites">
                    <thead class="table-light">
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Nom de l'université</th>
                            <th style="width:160px">Acronyme</th>
                            <th style="width:110px" class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($references as $ref)
                        <tr>
                            <td class="text-muted fs-13">{{ $loop->iteration }}</td>
                            <td><span class="font-w500">{{ $ref->nom }}</span></td>
                            <td>
                                @if($ref->acronyme)
                                    <code>{{ $ref->acronyme }}</code>
                                @else
                                    <span class="text-muted fs-13">—</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <button class="btn btn-xs btn-outline-primary me-1"
                                    data-bs-toggle="modal" data-bs-target="#modalEditerUniversite"
                                    data-id="{{ $ref->id }}"
                                    data-nom="{{ $ref->nom }}"
                                    data-acronyme="{{ $ref->acronyme ?? '' }}"
                                    title="Modifier">
                                    <i class="lni lni-pencil"></i>
                                </button>
                                <button class="btn btn-xs btn-outline-danger"
                                    data-bs-toggle="modal" data-bs-target="#modalSupprimerUniversite"
                                    data-id="{{ $ref->id }}"
                                    data-nom="{{ $ref->nom }}"
                                    title="Supprimer">
                                    <i class="lni lni-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr id="rowVide">
                            <td colspan="4" class="text-center text-muted py-5">
                                <i class="lni lni-library fs-2 d-block mb-2"></i>
                                Aucune université dans le référentiel.<br>
                                <button class="btn btn-sm btn-primary mt-3"
                                    data-bs-toggle="modal" data-bs-target="#modalCreerUniversite">
                                    <i class="lni lni-plus me-1"></i>Ajouter la première université
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($references->count() > 0)
            <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between">
                <span class="text-muted fs-13">
                    {{ $references->count() }} entrée{{ $references->count() !== 1 ? 's' : '' }} dans le référentiel
                </span>
            </div>
            @endif
        </div>
    </div>

</div>

{{-- ────────────────────────────────────────────
     Modal : Ajouter une université
──────────────────────────────────────────── --}}
<div class="modal fade" id="modalCreerUniversite" tabindex="-1" aria-labelledby="labelCreer">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="labelCreer">
                    <i class="lni lni-plus-circle me-2 text-primary"></i>Ajouter une université
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('superadmin.universites.creer') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nom de l'université <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nom"
                            class="form-control @error('nom') is-invalid @enderror"
                            value="{{ old('nom') }}"
                            placeholder="ex. Université Paris-Est"
                            autofocus required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold">Acronyme</label>
                        <input type="text" name="acronyme"
                            class="form-control @error('acronyme') is-invalid @enderror"
                            value="{{ old('acronyme') }}"
                            placeholder="ex. UPE" maxlength="20"
                            style="text-transform:uppercase;">
                        @error('acronyme')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Optionnel — 20 caractères max.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="lni lni-plus me-1"></i>Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ────────────────────────────────────────────
     Modal : Modifier une université
──────────────────────────────────────────── --}}
<div class="modal fade" id="modalEditerUniversite" tabindex="-1" aria-labelledby="labelEditer">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="labelEditer">
                    <i class="lni lni-pencil me-2 text-primary"></i>Modifier l'université
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditer" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nom de l'université <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nom" id="editNom"
                            class="form-control" required>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold">Acronyme</label>
                        <input type="text" name="acronyme" id="editAcronyme"
                            class="form-control" maxlength="20"
                            placeholder="ex. UPE"
                            style="text-transform:uppercase;">
                        <div class="form-text">Optionnel — 20 caractères max.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="lni lni-save me-1"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ────────────────────────────────────────────
     Modal : Supprimer une université
──────────────────────────────────────────── --}}
<div class="modal fade" id="modalSupprimerUniversite" tabindex="-1" aria-labelledby="labelSupprimer">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="labelSupprimer">
                    <i class="lni lni-trash me-2"></i>Confirmer la suppression
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formSupprimer" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p class="mb-1">Supprimer <strong id="nomSupprimer"></strong> du référentiel ?</p>
                    <p class="text-muted fs-13 mb-0">Cette action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="lni lni-trash me-1"></i>Supprimer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    /* ── Éditer ── */
    document.querySelectorAll('[data-bs-target="#modalEditerUniversite"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('editNom').value      = this.dataset.nom;
            document.getElementById('editAcronyme').value = this.dataset.acronyme;
            document.getElementById('formEditer').action  =
                '{{ url("superadmin/universites") }}/' + this.dataset.id;
        });
    });

    /* ── Supprimer ── */
    document.querySelectorAll('[data-bs-target="#modalSupprimerUniversite"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('nomSupprimer').textContent = this.dataset.nom;
            document.getElementById('formSupprimer').action =
                '{{ url("superadmin/universites") }}/' + this.dataset.id;
        });
    });

    /* ── Recherche côté client ── */
    const searchInput  = document.getElementById('searchUniversites');
    const compteur     = document.getElementById('compteur');
    const rows         = document.querySelectorAll('#tableUniversites tbody tr:not(#rowVide)');

    searchInput.addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        let visible = 0;
        rows.forEach(function (row) {
            const match = row.textContent.toLowerCase().includes(q);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        if (compteur) {
            compteur.textContent = visible + ' université' + (visible !== 1 ? 's' : '');
        }
    });

    /* ── Rouvrir le modal créer si erreur de validation ── */
    @if($errors->any() && old('_method') === null)
        var modal = new bootstrap.Modal(document.getElementById('modalCreerUniversite'));
        modal.show();
    @endif
})();
</script>
@endpush
