@extends('layouts.app')

@section('title', 'Annexes')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Annexes</h2>
            <p class="mb-0">Sites et campus de votre établissement</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreerAnnexe">
            <i class="lni lni-plus me-1"></i> Nouvelle annexe
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Grille des annexes --}}
    @if($annexes->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="lni lni-map-marker fs-1 d-block mb-3"></i>
                Aucune annexe enregistrée.<br>
                <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#modalCreerAnnexe">
                    <i class="lni lni-plus me-1"></i>Créer la première annexe
                </button>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($annexes as $annexe)
            <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header border-0 pb-0 d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0 font-w600">{{ $annexe->nom }}</h5>
                        <div class="dropdown">
                            <button class="btn btn-xs btn-outline-secondary" data-bs-toggle="dropdown">
                                <i class="lni lni-more-alt"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <button class="dropdown-item btn-editer-annexe"
                                        data-id="{{ $annexe->id }}"
                                        data-nom="{{ $annexe->nom }}"
                                        data-adresse="{{ $annexe->adresse }}"
                                        data-ville="{{ $annexe->ville }}"
                                        data-pays="{{ $annexe->pays }}"
                                        data-email="{{ $annexe->email }}"
                                        data-telephone="{{ $annexe->telephone }}"
                                        data-bs-toggle="modal" data-bs-target="#modalEditerAnnexe">
                                        <i class="lni lni-pencil me-2"></i>Modifier
                                    </button>
                                </li>
                                @if(!$annexe->gestionnaire)
                                <li>
                                    <button class="dropdown-item btn-creer-gestionnaire"
                                        data-id="{{ $annexe->id }}"
                                        data-nom="{{ $annexe->nom }}"
                                        data-bs-toggle="modal" data-bs-target="#modalGestionnaire">
                                        <i class="lni lni-user me-2"></i>Créer un gestionnaire
                                    </button>
                                </li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item text-danger btn-supprimer-annexe"
                                        data-id="{{ $annexe->id }}"
                                        data-nom="{{ $annexe->nom }}"
                                        data-bs-toggle="modal" data-bs-target="#modalSupprimerAnnexe">
                                        <i class="lni lni-trash me-2"></i>Supprimer
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-body pt-2">
                        {{-- Infos de l'annexe --}}
                        <ul class="list-unstyled mb-3">
                            @if($annexe->ville)
                            <li class="d-flex align-items-center gap-2 mb-1 text-muted fs-13">
                                <i class="lni lni-map-marker text-primary"></i>
                                {{ $annexe->adresse ? $annexe->adresse.', ' : '' }}{{ $annexe->ville }}{{ $annexe->pays ? ' ('.$annexe->pays.')' : '' }}
                            </li>
                            @endif
                            @if($annexe->email)
                            <li class="d-flex align-items-center gap-2 mb-1 text-muted fs-13">
                                <i class="lni lni-envelope text-primary"></i>{{ $annexe->email }}
                            </li>
                            @endif
                            @if($annexe->telephone)
                            <li class="d-flex align-items-center gap-2 text-muted fs-13">
                                <i class="lni lni-phone text-primary"></i>{{ $annexe->telephone }}
                            </li>
                            @endif
                        </ul>

                        {{-- Gestionnaire --}}
                        <div class="border-top pt-3">
                            <p class="fs-12 text-muted mb-2 text-uppercase fw-semibold">Gestionnaire</p>
                            @if($annexe->gestionnaire)
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-circle"
                                            style="width:36px;height:36px;font-size:14px;font-weight:600;flex-shrink:0;">
                                            {{ strtoupper(substr($annexe->gestionnaire->prenom, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-w500 fs-14">{{ $annexe->gestionnaire->prenom }} {{ $annexe->gestionnaire->nom }}</div>
                                            <div class="text-muted fs-12">{{ $annexe->gestionnaire->email }}</div>
                                        </div>
                                    </div>
                                    <form method="POST"
                                        action="{{ route('adminuniversity.annexes.gestionnaire.supprimer', $annexe->id) }}"
                                        onsubmit="return confirm('Retirer ce gestionnaire ?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-xs btn-outline-danger" title="Retirer">
                                            <i class="lni lni-close"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <button class="btn btn-sm btn-outline-primary w-100 btn-creer-gestionnaire"
                                    data-id="{{ $annexe->id }}"
                                    data-nom="{{ $annexe->nom }}"
                                    data-bs-toggle="modal" data-bs-target="#modalGestionnaire">
                                    <i class="lni lni-user me-1"></i>Assigner un gestionnaire
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>

{{-- ─────────────────────────────────────────────
     Modal : Créer une annexe
───────────────────────────────────────────── --}}
<div class="modal fade" id="modalCreerAnnexe" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="lni lni-map-marker me-2 text-primary"></i>Nouvelle annexe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('adminuniversity.annexes.creer') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nom du site / campus <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control" placeholder="ex. Campus Nord" required autofocus>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Adresse</label>
                            <input type="text" name="adresse" class="form-control" placeholder="Rue et numéro">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Ville</label>
                            <input type="text" name="ville" class="form-control" placeholder="Cotonou">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Pays</label>
                            <input type="text" name="pays" class="form-control" placeholder="Bénin">
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="annexe@univ.fr">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <input type="text" name="telephone" class="form-control" placeholder="+229 XX XX XX XX">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="lni lni-plus me-1"></i>Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ─────────────────────────────────────────────
     Modal : Modifier une annexe
───────────────────────────────────────────── --}}
<div class="modal fade" id="modalEditerAnnexe" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="lni lni-pencil me-2 text-primary"></i>Modifier l'annexe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditerAnnexe" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nom du site / campus <span class="text-danger">*</span></label>
                        <input type="text" id="editNom" name="nom" class="form-control" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Adresse</label>
                            <input type="text" id="editAdresse" name="adresse" class="form-control">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Ville</label>
                            <input type="text" id="editVille" name="ville" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Pays</label>
                            <input type="text" id="editPays" name="pays" class="form-control">
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" id="editEmail" name="email" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <input type="text" id="editTelephone" name="telephone" class="form-control">
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

{{-- ─────────────────────────────────────────────
     Modal : Créer un gestionnaire
───────────────────────────────────────────── --}}
<div class="modal fade" id="modalGestionnaire" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="lni lni-user me-2 text-primary"></i>Créer un gestionnaire</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formGestionnaire" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info fs-13 py-2 mb-3">
                        Annexe : <strong id="labelAnnexeGestionnaire"></strong>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="prenom" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold">Mot de passe <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" id="inputMotDePasse" name="password"
                                class="form-control" minlength="8" required
                                placeholder="Minimum 8 caractères">
                            <button type="button" class="btn btn-outline-secondary" id="btnGenPassword" title="Générer">
                                <i class="lni lni-reload"></i>
                            </button>
                        </div>
                        <div class="form-text">Ce mot de passe sera communiqué au gestionnaire.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="lni lni-checkmark me-1"></i>Créer le compte</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ─────────────────────────────────────────────
     Modal : Supprimer une annexe
───────────────────────────────────────────── --}}
<div class="modal fade" id="modalSupprimerAnnexe" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="lni lni-trash me-2"></i>Supprimer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formSupprimerAnnexe" method="POST">
                @csrf @method('DELETE')
                <div class="modal-body">
                    <p class="mb-1">Supprimer l'annexe <strong id="nomSupprimerAnnexe"></strong> ?</p>
                    <p class="text-muted fs-13 mb-0">Les utilisateurs rattachés seront déliés. Action irréversible.</p>
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
(function () {
    const base = '{{ url("adminuniversity/annexes") }}';

    /* ── Modifier annexe ── */
    document.querySelectorAll('.btn-editer-annexe').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('editNom').value        = this.dataset.nom       ?? '';
            document.getElementById('editAdresse').value   = this.dataset.adresse   ?? '';
            document.getElementById('editVille').value     = this.dataset.ville     ?? '';
            document.getElementById('editPays').value      = this.dataset.pays      ?? '';
            document.getElementById('editEmail').value     = this.dataset.email     ?? '';
            document.getElementById('editTelephone').value = this.dataset.telephone ?? '';
            document.getElementById('formEditerAnnexe').action = base + '/' + this.dataset.id;
        });
    });

    /* ── Gestionnaire ── */
    document.querySelectorAll('.btn-creer-gestionnaire').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('labelAnnexeGestionnaire').textContent = this.dataset.nom;
            document.getElementById('formGestionnaire').action = base + '/' + this.dataset.id + '/gestionnaire';
        });
    });

    /* ── Supprimer annexe ── */
    document.querySelectorAll('.btn-supprimer-annexe').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('nomSupprimerAnnexe').textContent = this.dataset.nom;
            document.getElementById('formSupprimerAnnexe').action = base + '/' + this.dataset.id;
        });
    });

    /* ── Générateur de mot de passe ── */
    document.getElementById('btnGenPassword').addEventListener('click', function () {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789@#!';
        let pwd = '';
        for (let i = 0; i < 10; i++) {
            pwd += chars[Math.floor(Math.random() * chars.length)];
        }
        document.getElementById('inputMotDePasse').value = pwd;
        document.getElementById('inputMotDePasse').type = 'text';
    });
})();
</script>
@endpush
