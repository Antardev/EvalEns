@extends('layouts.app')

@section('title', 'Formations & UE')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Formations &amp; UE</h2>
            <p class="mb-0">Gérer les formations et leurs unités d'enseignement</p>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreerFormation">
            <i class="lni lni-plus me-1"></i>Nouvelle formation
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
    $formations = [
        ['id' => 1, 'nom' => 'L3 Informatique', 'annee' => 'L3', 'etudiants' => 28, 'ues' => [
            ['Algorithmique avancée', 'J. Martin', 6],
            ['Bases de données', 'C. Moreau', 4],
            ['Réseaux informatiques', 'M. Lefebvre', 4],
        ]],
        ['id' => 2, 'nom' => 'M1 Mathématiques', 'annee' => 'M1', 'etudiants' => 19, 'ues' => [
            ['Analyse fonctionnelle', 'C. Moreau', 6],
            ['Probabilités avancées', 'P. Rousseau', 5],
        ]],
        ['id' => 3, 'nom' => 'M2 Intelligence Artificielle', 'annee' => 'M2', 'etudiants' => 15, 'ues' => [
            ['Deep Learning', 'S. Garnier', 6],
            ['Traitement du langage naturel', 'J. Martin', 6],
            ['Vision par ordinateur', 'M. Lefebvre', 4],
        ]],
        ['id' => 4, 'nom' => 'M1 Data Science', 'annee' => 'M1', 'etudiants' => 22, 'ues' => [
            ['Statistiques avancées', 'P. Rousseau', 5],
            ['Machine Learning', 'S. Garnier', 6],
        ]],
    ];
    @endphp

    @foreach($formations as $formation)
    <div class="card mb-3">
        <div class="card-header border-0 pb-0 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <span class="badge badge-primary px-3">{{ $formation['annee'] }}</span>
                <h5 class="mb-0 font-w600">{{ $formation['nom'] }}</h5>
                <small class="text-muted">{{ $formation['etudiants'] }} étudiants</small>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-xs btn-outline-success"
                    data-bs-toggle="modal" data-bs-target="#modalAjouterUE"
                    data-formation-id="{{ $formation['id'] }}" data-formation-nom="{{ $formation['nom'] }}">
                    <i class="lni lni-plus me-1"></i>Ajouter UE
                </button>
                <button class="btn btn-xs btn-outline-primary"
                    data-bs-toggle="modal" data-bs-target="#modalEditerFormation"
                    data-id="{{ $formation['id'] }}" data-nom="{{ $formation['nom'] }}">
                    <i class="lni lni-pencil"></i>
                </button>
                <button class="btn btn-xs btn-outline-danger"
                    data-bs-toggle="modal" data-bs-target="#modalSupprimerFormation"
                    data-id="{{ $formation['id'] }}" data-nom="{{ $formation['nom'] }}">
                    <i class="lni lni-trash"></i>
                </button>
            </div>
        </div>
        <div class="card-body pt-2">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Unité d'enseignement</th>
                        <th>Enseignant responsable</th>
                        <th class="text-center">ECTS</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($formation['ues'] as [$nomUE, $enseignant, $ects])
                    <tr>
                        <td class="font-w500 fs-14">{{ $nomUE }}</td>
                        <td class="text-muted fs-13">{{ $enseignant }}</td>
                        <td class="text-center"><span class="badge badge-xs badge-info">{{ $ects }} ECTS</span></td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-outline-primary me-1"><i class="lni lni-pencil"></i></button>
                            <button class="btn btn-xs btn-outline-danger"><i class="lni lni-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

</div>

{{-- Modal : Créer Formation --}}
<div class="modal fade" id="modalCreerFormation" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Nouvelle formation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('adminuniversity.formations.creer') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label fw-semibold">Nom de la formation <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control" placeholder="ex. M2 Cybersécurité" required></div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label fw-semibold">Niveau</label>
                            <select name="annee" class="form-select">
                                <option>L1</option><option>L2</option><option>L3</option>
                                <option>M1</option><option>M2</option></select></div>
                        <div class="col-md-6"><label class="form-label fw-semibold">Département</label>
                            <select name="departement" class="form-select">
                                <option>Informatique</option><option>Mathématiques</option>
                                <option>Physique</option><option>Data Science</option></select></div>
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

{{-- Modal : Ajouter UE --}}
<div class="modal fade" id="modalAjouterUE" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Ajouter une UE à <span id="nomFormationUE"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="#">
                @csrf
                <input type="hidden" id="formationIdUE" name="formation_id">
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label fw-semibold">Nom de l'UE <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control" required></div>
                    <div class="row g-3">
                        <div class="col-md-8"><label class="form-label fw-semibold">Enseignant responsable</label>
                            <select name="enseignant_id" class="form-select">
                                <option>Jean Martin</option><option>Claire Moreau</option>
                                <option>Pierre Rousseau</option><option>Sophie Garnier</option></select></div>
                        <div class="col-md-4"><label class="form-label fw-semibold">ECTS</label>
                            <input type="number" name="ects" class="form-control" value="4" min="1" max="30"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success"><i class="lni lni-plus me-1"></i>Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal : Éditer Formation --}}
<div class="modal fade" id="modalEditerFormation" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Modifier la formation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="formEditerFormation" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <label class="form-label fw-semibold">Nom</label>
                    <input type="text" name="nom" id="fEditNom" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-sm">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal : Supprimer Formation --}}
<div class="modal fade" id="modalSupprimerFormation" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title text-danger">Supprimer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="formSupprimerFormation" method="POST">
                @csrf @method('DELETE')
                <div class="modal-body"><p>Supprimer <strong id="nomSupprimerFormation"></strong> et toutes ses UE ?</p></div>
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
    document.querySelectorAll('[data-bs-target="#modalAjouterUE"]').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('nomFormationUE').textContent = this.dataset.formationNom;
            document.getElementById('formationIdUE').value = this.dataset.formationId;
        });
    });
    document.querySelectorAll('[data-bs-target="#modalEditerFormation"]').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('fEditNom').value = this.dataset.nom;
            document.getElementById('formEditerFormation').action = '/adminuniversity/formations/' + this.dataset.id;
        });
    });
    document.querySelectorAll('[data-bs-target="#modalSupprimerFormation"]').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('nomSupprimerFormation').textContent = this.dataset.nom;
            document.getElementById('formSupprimerFormation').action = '/adminuniversity/formations/' + this.dataset.id;
        });
    });
</script>
@endpush
