@extends('layouts.app')

@section('title', 'Configuration des critères')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Configuration des critères</h2>
            <p class="mb-0">{{ $annexe->nom }} — Personnalisez les critères d'évaluation</p>
        </div>
        <a href="{{ route('gestionnaire.liens') }}" class="btn btn-outline-secondary btn-sm">
            <i class="lni lni-link me-1"></i>Retour aux questionnaires
        </a>
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
                    <small class="text-muted">La somme des pondérations doit être égale à 100%.</small>
                </div>
                <div class="card-body">

                    @if(!$hasOwn)
                    <div class="alert alert-info fs-13 mb-4">
                        <i class="lni lni-information me-2"></i>
                        Critères hérités par défaut — modifiez-les pour personnaliser votre annexe. Une copie sera créée pour votre université.
                    </div>
                    @else
                    <div class="alert alert-success fs-13 mb-4">
                        <i class="lni lni-checkmark-circle me-2"></i>
                        Critères personnalisés actifs pour cette université.
                    </div>
                    @endif

                    <form method="POST" action="{{ route('gestionnaire.questionnaires.save') }}">
                        @csrf

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
                                    @foreach($criteres as $i => $critere)
                                    <tr>
                                        <td><input type="text" name="criteres[{{ $i }}][nom]"
                                            class="form-control form-control-sm" value="{{ $critere->nom }}" required></td>
                                        <td><input type="text" name="criteres[{{ $i }}][description]"
                                            class="form-control form-control-sm" value="{{ $critere->description }}"></td>
                                        <td><input type="number" name="criteres[{{ $i }}][poids]"
                                            class="form-control form-control-sm critere-poids"
                                            value="{{ $critere->poids }}" min="0" max="100" required></td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox"
                                                    name="criteres[{{ $i }}][actif]" value="1"
                                                    {{ $critere->actif ? 'checked' : '' }}>
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
                                <span id="totalPoids" class="fs-16 font-w600 text-primary">0%</span>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="lni lni-save me-1"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-0">Répartition</h4>
                </div>
                <div class="card-body">
                    <canvas id="chartCriteres" height="240"></canvas>
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
        document.querySelectorAll('.critere-poids').forEach(i => total += parseInt(i.value) || 0);
        const el = document.getElementById('totalPoids');
        el.textContent = total + '%';
        el.className = 'fs-16 font-w600 ' + (total === 100 ? 'text-success' : 'text-danger');
        updateChart();
    }

    document.querySelectorAll('.critere-poids').forEach(i => i.addEventListener('input', updateTotal));
    document.querySelectorAll('.btn-rm').forEach(btn => {
        btn.addEventListener('click', function () { this.closest('tr').remove(); updateTotal(); });
    });

    var rowIndex = {{ $criteres->count() }};
    document.getElementById('btnAjouterCritere').addEventListener('click', function () {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" name="criteres[${rowIndex}][nom]" class="form-control form-control-sm" required></td>
            <td><input type="text" name="criteres[${rowIndex}][description]" class="form-control form-control-sm"></td>
            <td><input type="number" name="criteres[${rowIndex}][poids]" class="form-control form-control-sm critere-poids" value="0" min="0" max="100" required></td>
            <td class="text-center"><div class="form-check form-switch d-inline-block"><input class="form-check-input" type="checkbox" name="criteres[${rowIndex}][actif]" value="1" checked></div></td>
            <td><button type="button" class="btn btn-xs btn-outline-danger btn-rm"><i class="lni lni-trash"></i></button></td>
        `;
        tr.querySelector('.btn-rm').addEventListener('click', function () { this.closest('tr').remove(); updateTotal(); });
        tr.querySelector('.critere-poids').addEventListener('input', updateTotal);
        document.getElementById('criteresBody').appendChild(tr);
        rowIndex++;
        updateTotal();
    });

    function updateChart() {
        const labels = [], data = [], colors = [
            '#2F4CDD','#2BC155','#FFAB2D','#FF2E2E','#6c757d','#17a2b8',
            '#e83e8c','#fd7e14','#20c997','#6f42c1','#0dcaf0','#198754','#dc3545','#ffc107'
        ];
        document.querySelectorAll('#criteresBody tr').forEach(row => {
            const nom   = row.querySelector('[name*="nom"]')?.value || '';
            const poids = parseInt(row.querySelector('.critere-poids')?.value) || 0;
            if (poids > 0) { labels.push(nom); data.push(poids); }
        });
        if (chartCriteres) chartCriteres.destroy();
        chartCriteres = new Chart(document.getElementById('chartCriteres'), {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{ data, backgroundColor: colors.slice(0, data.length), borderWidth: 2, borderColor: '#fff' }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } },
                cutout: '55%'
            }
        });
    }

    updateTotal();
</script>
@endpush
