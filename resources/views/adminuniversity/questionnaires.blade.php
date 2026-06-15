@extends('layouts.app')

@section('title', 'Questionnaires')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Configuration des questionnaires</h2>
            <p class="mb-0">Personnaliser les critères d'évaluation pour votre université</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-8 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Critères d'évaluation</h4>
                    <small class="text-muted">
                        Vous pouvez personnaliser les critères par défaut définis par le SuperAdmin.
                        La somme des pondérations doit être égale à 100%.
                    </small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('adminuniversity.questionnaires.save') }}">
                        @csrf

                        <div class="alert alert-info fs-13 mb-4">
                            <i class="lni lni-information me-2"></i>
                            Critères hérités du SuperAdmin — vous pouvez les modifier ou en ajouter.
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover mb-3" id="tableCriteres">
                                <thead>
                                    <tr>
                                        <th style="width:35%">Critère</th>
                                        <th style="width:30%">Description</th>
                                        <th style="width:12%">Poids (%)</th>
                                        <th style="width:13%">Actif</th>
                                        <th style="width:10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="criteresBody">
                                    @php
                                    $criteres = [
                                        ['Qualité pédagogique', 'Clarté des explications', 30, true],
                                        ['Organisation du cours', 'Structure, supports fournis', 25, true],
                                        ['Communication', 'Interaction avec les étudiants', 20, true],
                                        ['Disponibilité', 'Accessibilité hors cours', 15, true],
                                        ["Équité de l'évaluation", 'Impartialité des notes', 10, true],
                                    ];
                                    @endphp
                                    @foreach($criteres as $i => [$nom, $desc, $poids, $actif])
                                    <tr>
                                        <td><input type="text" name="criteres[{{ $i }}][nom]"
                                            class="form-control form-control-sm" value="{{ $nom }}" required></td>
                                        <td><input type="text" name="criteres[{{ $i }}][description]"
                                            class="form-control form-control-sm" value="{{ $desc }}"></td>
                                        <td><input type="number" name="criteres[{{ $i }}][poids]"
                                            class="form-control form-control-sm critere-poids"
                                            value="{{ $poids }}" min="0" max="100" required></td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox"
                                                    name="criteres[{{ $i }}][actif]" value="1" {{ $actif ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-xs btn-outline-danger btn-rm">
                                                <i class="lni lni-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAjouterCritere">
                                <i class="lni lni-plus me-1"></i>Ajouter un critère
                            </button>
                            <div>
                                <span class="fs-14 me-2">Total :</span>
                                <span id="totalPoids" class="fs-16 font-w600 text-primary">100%</span>
                            </div>
                        </div>

                        <hr>

                        <h5 class="mb-3">Paramètres du questionnaire</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Commentaire textuel</label>
                                <select name="commentaire" class="form-select">
                                    <option value="optionnel">Optionnel</option>
                                    <option value="obligatoire">Obligatoire</option>
                                    <option value="desactive">Désactivé</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Longueur max. du commentaire</label>
                                <input type="number" name="commentaire_max" class="form-control" value="500" min="50" max="2000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Anonymat garanti</label>
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" name="anonymat" value="1" id="chkAnonyme" checked>
                                    <label class="form-check-label" for="chkAnonyme">
                                        Cacher l'identité de l'étudiant à l'enseignant
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Seuil de confidentialité</label>
                                <div class="input-group">
                                    <input type="number" name="seuil_confidentialite" class="form-control" value="5" min="3" max="20">
                                    <span class="input-group-text">réponses min.</span>
                                </div>
                                <small class="text-muted">Résultats masqués si moins de N réponses</small>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="lni lni-save me-1"></i>Enregistrer la configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-0">Aperçu</h4>
                </div>
                <div class="card-body">
                    <canvas id="chartCriteres" height="220"></canvas>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Aperçu étudiant</h5>
                    <p class="text-muted fs-13">Le formulaire présentera :</p>
                    <ul class="fs-13 text-muted">
                        <li>5 critères de notation (échelle 1–5)</li>
                        <li>Un champ commentaire optionnel (max. 500 caractères)</li>
                        <li>Bouton « Enregistrer brouillon »</li>
                        <li>Bouton « Soumettre définitivement »</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('dashboard/vendor/chart-js/chart.bundle.min.js') }}"></script>
<script>
    var chartCriteres;
    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.critere-poids').forEach(i => total += parseInt(i.value)||0);
        const el = document.getElementById('totalPoids');
        el.textContent = total + '%';
        el.className = 'fs-16 font-w600 ' + (total===100 ? 'text-primary' : 'text-danger');
        updateChart();
    }
    document.querySelectorAll('.critere-poids').forEach(i => i.addEventListener('input', updateTotal));
    document.querySelectorAll('.btn-rm').forEach(btn => {
        btn.addEventListener('click', function() { this.closest('tr').remove(); updateTotal(); });
    });

    var rowIndex = {{ count($criteres ?? []) }};
    document.getElementById('btnAjouterCritere').addEventListener('click', function() {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" name="criteres[${rowIndex}][nom]" class="form-control form-control-sm" required></td>
            <td><input type="text" name="criteres[${rowIndex}][description]" class="form-control form-control-sm"></td>
            <td><input type="number" name="criteres[${rowIndex}][poids]" class="form-control form-control-sm critere-poids" value="10" min="0" max="100" required></td>
            <td class="text-center"><div class="form-check form-switch d-inline-block"><input class="form-check-input" type="checkbox" name="criteres[${rowIndex}][actif]" value="1" checked></div></td>
            <td><button type="button" class="btn btn-xs btn-outline-danger btn-rm"><i class="lni lni-trash"></i></button></td>
        `;
        tr.querySelector('.btn-rm').addEventListener('click', function() { this.closest('tr').remove(); updateTotal(); });
        tr.querySelector('.critere-poids').addEventListener('input', updateTotal);
        document.getElementById('criteresBody').appendChild(tr);
        rowIndex++;
        updateTotal();
    });

    function updateChart() {
        const labels = [], data = [];
        document.querySelectorAll('#criteresBody tr').forEach(row => {
            const nom = row.querySelector('[name*="nom"]')?.value || '';
            const poids = parseInt(row.querySelector('.critere-poids')?.value)||0;
            labels.push(nom); data.push(poids);
        });
        if (chartCriteres) chartCriteres.destroy();
        chartCriteres = new Chart(document.getElementById('chartCriteres'), {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{ data, backgroundColor:['#2F4CDD','#2BC155','#FFAB2D','#FF2E2E','#6c757d','#17a2b8'], borderWidth:2, borderColor:'#fff' }]
            },
            options: { responsive:true, plugins:{ legend:{ position:'bottom', labels:{ font:{size:11} } } }, cutout:'55%' }
        });
    }
    updateChart();
</script>
@endpush
