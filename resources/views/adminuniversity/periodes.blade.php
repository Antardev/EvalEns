@extends('layouts.app')

@section('title', "Périodes d'évaluation")

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Périodes d'évaluation</h2>
            <p class="mb-0">Paramétrage des fenêtres d'évaluation</p>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreerPeriode">
            <i class="lni lni-plus me-1"></i>Nouvelle période
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nom de la période</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Formations concernées</th>
                            <th>Participation</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-w500">S2 2025-2026</td>
                            <td>01/02/2026</td>
                            <td>31/05/2026</td>
                            <td>Toutes</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:6px;">
                                        <div class="progress-bar bg-success" style="width:65%"></div>
                                    </div>
                                    <span class="fs-12">65%</span>
                                </div>
                            </td>
                            <td><span class="badge badge-sm badge-success">Active</span></td>
                            <td>
                                <button class="btn btn-xs btn-outline-warning me-1" title="Clôturer">
                                    <i class="lni lni-lock-alt"></i>
                                </button>
                                <button class="btn btn-xs btn-outline-primary me-1"
                                    data-bs-toggle="modal" data-bs-target="#modalEditerPeriode"
                                    data-id="1" data-nom="S2 2025-2026"
                                    data-debut="2026-02-01" data-fin="2026-05-31">
                                    <i class="lni lni-pencil"></i>
                                </button>
                                <button class="btn btn-xs btn-outline-danger"
                                    data-bs-toggle="modal" data-bs-target="#modalSupprimerPeriode"
                                    data-id="1" data-nom="S2 2025-2026">
                                    <i class="lni lni-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-w500">Rattrapage Juin 2026</td>
                            <td>01/06/2026</td>
                            <td>15/06/2026</td>
                            <td>L3 Info, M1 Maths</td>
                            <td><span class="text-muted fs-12">Pas encore débutée</span></td>
                            <td><span class="badge badge-sm badge-warning">À venir</span></td>
                            <td>
                                <button class="btn btn-xs btn-outline-primary me-1"
                                    data-bs-toggle="modal" data-bs-target="#modalEditerPeriode"
                                    data-id="2" data-nom="Rattrapage Juin 2026"
                                    data-debut="2026-06-01" data-fin="2026-06-15">
                                    <i class="lni lni-pencil"></i>
                                </button>
                                <button class="btn btn-xs btn-outline-danger"
                                    data-bs-toggle="modal" data-bs-target="#modalSupprimerPeriode"
                                    data-id="2" data-nom="Rattrapage Juin 2026">
                                    <i class="lni lni-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-w500">S1 2025-2026</td>
                            <td>15/09/2025</td>
                            <td>20/01/2026</td>
                            <td>Toutes</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:6px;">
                                        <div class="progress-bar bg-primary" style="width:82%"></div>
                                    </div>
                                    <span class="fs-12">82%</span>
                                </div>
                            </td>
                            <td><span class="badge badge-sm badge-secondary">Clôturée</span></td>
                            <td>
                                <a href="{{ route('adminuniversity.rapports') }}" class="btn btn-xs btn-outline-info" title="Voir rapport">
                                    <i class="lni lni-eye"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal : Créer --}}
<div class="modal fade" id="modalCreerPeriode" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle période d'évaluation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('adminuniversity.periodes.creer') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control" placeholder="ex. S1 2026-2027" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date de début <span class="text-danger">*</span></label>
                            <input type="date" name="date_debut" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date de fin <span class="text-danger">*</span></label>
                            <input type="date" name="date_fin" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label class="form-label fw-semibold">Formations concernées</label>
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="formations[]" value="all" id="fAll" checked>
                            <label class="form-check-label" for="fAll">Toutes les formations</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="formations[]" value="l3info" id="fL3">
                            <label class="form-check-label" for="fL3">L3 Informatique</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="formations[]" value="m1maths" id="fM1">
                            <label class="form-check-label" for="fM1">M1 Mathématiques</label></div>
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

{{-- Modal : Éditer --}}
<div class="modal fade" id="modalEditerPeriode" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier la période</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditerPeriode" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nom</label>
                        <input type="text" name="nom" id="pEditNom" class="form-control" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label fw-semibold">Date début</label>
                            <input type="date" name="date_debut" id="pEditDebut" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Date fin</label>
                            <input type="date" name="date_fin" id="pEditFin" class="form-control" required></div>
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
<div class="modal fade" id="modalSupprimerPeriode" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title text-danger">Supprimer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="formSupprimerPeriode" method="POST">
                @csrf @method('DELETE')
                <div class="modal-body"><p>Supprimer la période <strong id="nomSupprimerPeriode"></strong> ?</p>
                    <p class="text-danger fs-12">Les évaluations associées seront perdues.</p></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-bs-target="#modalEditerPeriode"]').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('pEditNom').value   = this.dataset.nom;
            document.getElementById('pEditDebut').value = this.dataset.debut;
            document.getElementById('pEditFin').value   = this.dataset.fin;
            document.getElementById('formEditerPeriode').action = '/adminuniversity/periodes/' + this.dataset.id;
        });
    });
    document.querySelectorAll('[data-bs-target="#modalSupprimerPeriode"]').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('nomSupprimerPeriode').textContent = this.dataset.nom;
            document.getElementById('formSupprimerPeriode').action = '/adminuniversity/periodes/' + this.dataset.id;
        });
    });
</script>
@endpush
