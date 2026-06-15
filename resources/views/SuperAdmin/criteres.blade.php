@extends('layouts.superadmin')

@section('title', "Critères d'évaluation")

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Critères d'évaluation</h2>
            <p class="mb-0">Configuration des critères par défaut appliqués à toutes les universités</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">

        {{-- Tableau des critères actuels --}}
        <div class="col-xl-8 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0 d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-1">Critères actuels</h4>
                        <small class="text-muted">La somme des pondérations doit être égale à 100%</small>
                    </div>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAjouterCritere">
                        <i class="lni lni-plus me-1"></i>Ajouter
                    </button>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('superadmin.criteres.save') }}" id="formCriteres">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-hover mb-3">
                                <thead>
                                    <tr>
                                        <th style="width:40%">Critère</th>
                                        <th style="width:35%">Description</th>
                                        <th style="width:12%">Pondération (%)</th>
                                        <th style="width:13%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="criteresBody">
                                    <tr data-id="1">
                                        <td>
                                            <input type="text" name="criteres[0][nom]" class="form-control form-control-sm"
                                                value="Qualité pédagogique" required>
                                        </td>
                                        <td>
                                            <input type="text" name="criteres[0][description]" class="form-control form-control-sm"
                                                value="Clarté des explications, pertinence des exemples" placeholder="Description...">
                                        </td>
                                        <td>
                                            <input type="number" name="criteres[0][poids]" class="form-control form-control-sm critere-poids"
                                                value="30" min="0" max="100" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-xs btn-outline-danger btn-suppr-critere" title="Supprimer">
                                                <i class="lni lni-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr data-id="2">
                                        <td>
                                            <input type="text" name="criteres[1][nom]" class="form-control form-control-sm"
                                                value="Organisation du cours" required>
                                        </td>
                                        <td>
                                            <input type="text" name="criteres[1][description]" class="form-control form-control-sm"
                                                value="Structure, respect du programme, supports fournis" placeholder="Description...">
                                        </td>
                                        <td>
                                            <input type="number" name="criteres[1][poids]" class="form-control form-control-sm critere-poids"
                                                value="25" min="0" max="100" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-xs btn-outline-danger btn-suppr-critere" title="Supprimer">
                                                <i class="lni lni-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr data-id="3">
                                        <td>
                                            <input type="text" name="criteres[2][nom]" class="form-control form-control-sm"
                                                value="Communication" required>
                                        </td>
                                        <td>
                                            <input type="text" name="criteres[2][description]" class="form-control form-control-sm"
                                                value="Interaction avec les étudiants, écoute active" placeholder="Description...">
                                        </td>
                                        <td>
                                            <input type="number" name="criteres[2][poids]" class="form-control form-control-sm critere-poids"
                                                value="20" min="0" max="100" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-xs btn-outline-danger btn-suppr-critere" title="Supprimer">
                                                <i class="lni lni-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr data-id="4">
                                        <td>
                                            <input type="text" name="criteres[3][nom]" class="form-control form-control-sm"
                                                value="Disponibilité" required>
                                        </td>
                                        <td>
                                            <input type="text" name="criteres[3][description]" class="form-control form-control-sm"
                                                value="Accessibilité hors cours, réactivité aux questions" placeholder="Description...">
                                        </td>
                                        <td>
                                            <input type="number" name="criteres[3][poids]" class="form-control form-control-sm critere-poids"
                                                value="15" min="0" max="100" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-xs btn-outline-danger btn-suppr-critere" title="Supprimer">
                                                <i class="lni lni-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr data-id="5">
                                        <td>
                                            <input type="text" name="criteres[4][nom]" class="form-control form-control-sm"
                                                value="Équité de l'évaluation" required>
                                        </td>
                                        <td>
                                            <input type="text" name="criteres[4][description]" class="form-control form-control-sm"
                                                value="Impartialité des notes, clarté des critères d'évaluation" placeholder="Description...">
                                        </td>
                                        <td>
                                            <input type="number" name="criteres[4][poids]" class="form-control form-control-sm critere-poids"
                                                value="10" min="0" max="100" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-xs btn-outline-danger btn-suppr-critere" title="Supprimer">
                                                <i class="lni lni-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="fs-14 me-2">Total des pondérations :</span>
                                <span id="totalPoids" class="fs-16 font-w600 text-primary">100%</span>
                                <span id="poidsWarning" class="text-danger ms-2 d-none">⚠ La somme doit être égale à 100%</span>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="lni lni-save me-1"></i>Enregistrer les critères
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Aperçu + infos --}}
        <div class="col-xl-4 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Aperçu</h4>
                </div>
                <div class="card-body">
                    <canvas id="chartCriteres" height="220"></canvas>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Informations</h5>
                    <ul class="list-unstyled fs-13 text-muted">
                        <li class="mb-2"><i class="lni lni-information me-2 text-primary"></i>Ces critères s'appliquent par défaut à toutes les universités.</li>
                        <li class="mb-2"><i class="lni lni-information me-2 text-primary"></i>Chaque université peut personnaliser ses propres critères.</li>
                        <li class="mb-2"><i class="lni lni-information me-2 text-primary"></i>Les étudiants évaluent chaque critère sur une échelle de 1 à 5.</li>
                        <li><i class="lni lni-information me-2 text-primary"></i>La modification des critères ne rétroagit pas sur les évaluations passées.</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal : Ajouter un critère --}}
<div class="modal fade" id="modalAjouterCritere" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un critère</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nom du critère <span class="text-danger">*</span></label>
                    <input type="text" id="newCritereNom" class="form-control" placeholder="ex. Maîtrise de la matière">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <input type="text" id="newCritereDesc" class="form-control" placeholder="Description courte...">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pondération (%) <span class="text-danger">*</span></label>
                    <input type="number" id="newCriterePoids" class="form-control" value="10" min="1" max="100">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="btnAjouterCritere">
                    <i class="lni lni-plus me-1"></i>Ajouter
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('dashboard/vendor/chart-js/chart.bundle.min.js') }}"></script>
<script>
    // Calcul du total des pondérations
    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.critere-poids').forEach(input => {
            total += parseInt(input.value) || 0;
        });
        const el = document.getElementById('totalPoids');
        const warn = document.getElementById('poidsWarning');
        el.textContent = total + '%';
        el.className = 'fs-16 font-w600 ' + (total === 100 ? 'text-primary' : 'text-danger');
        warn.classList.toggle('d-none', total === 100);
        updateChart();
    }

    document.querySelectorAll('.critere-poids').forEach(input => {
        input.addEventListener('input', updateTotal);
    });

    document.querySelectorAll('.btn-suppr-critere').forEach(btn => {
        btn.addEventListener('click', function () {
            if (document.querySelectorAll('#criteresBody tr').length > 1) {
                this.closest('tr').remove();
                updateTotal();
                renumberCriteres();
            }
        });
    });

    function renumberCriteres() {
        document.querySelectorAll('#criteresBody tr').forEach((row, idx) => {
            row.querySelectorAll('[name]').forEach(input => {
                input.name = input.name.replace(/criteres\[\d+\]/, 'criteres[' + idx + ']');
            });
        });
    }

    // Ajouter un critère via modal
    document.getElementById('btnAjouterCritere').addEventListener('click', function () {
        const nom   = document.getElementById('newCritereNom').value.trim();
        const desc  = document.getElementById('newCritereDesc').value.trim();
        const poids = parseInt(document.getElementById('newCriterePoids').value) || 0;
        if (!nom) return;

        const idx = document.querySelectorAll('#criteresBody tr').length;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="text" name="criteres[${idx}][nom]" class="form-control form-control-sm" value="${nom}" required></td>
            <td><input type="text" name="criteres[${idx}][description]" class="form-control form-control-sm" value="${desc}" placeholder="Description..."></td>
            <td><input type="number" name="criteres[${idx}][poids]" class="form-control form-control-sm critere-poids" value="${poids}" min="0" max="100" required></td>
            <td><button type="button" class="btn btn-xs btn-outline-danger btn-suppr-critere" title="Supprimer"><i class="lni lni-trash"></i></button></td>
        `;
        row.querySelector('.btn-suppr-critere').addEventListener('click', function () {
            this.closest('tr').remove();
            updateTotal();
            renumberCriteres();
        });
        row.querySelector('.critere-poids').addEventListener('input', updateTotal);
        document.getElementById('criteresBody').appendChild(row);
        updateTotal();

        document.getElementById('newCritereNom').value = '';
        document.getElementById('newCritereDesc').value = '';
        document.getElementById('newCriterePoids').value = '10';
        bootstrap.Modal.getInstance(document.getElementById('modalAjouterCritere')).hide();
    });

    // Graphique Doughnut
    var chartCriteres;
    function updateChart() {
        const labels = [];
        const data   = [];
        document.querySelectorAll('#criteresBody tr').forEach(row => {
            const nom   = row.querySelector('[name*="nom"]')?.value || '';
            const poids = parseInt(row.querySelector('.critere-poids')?.value) || 0;
            labels.push(nom);
            data.push(poids);
        });

        if (chartCriteres) chartCriteres.destroy();

        const ctx = document.getElementById('chartCriteres').getContext('2d');
        chartCriteres = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: ['#2F4CDD','#2BC155','#FFAB2D','#FF2E2E','#6c757d','#17a2b8','#e83e8c'],
                    borderWidth: 2, borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } },
                cutout: '55%',
            }
        });
    }

    updateChart();
</script>
@endpush
