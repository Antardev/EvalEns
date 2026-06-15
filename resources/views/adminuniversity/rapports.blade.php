@extends('layouts.app')

@section('title', 'Rapports & Exports')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Rapports &amp; Exports PDF</h2>
            <p class="mb-0">Consulter et exporter les rapports d'évaluation</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-4 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Générer un rapport</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('adminuniversity.rapports.exporter') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Type de rapport <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" id="typeRapport" required>
                                <option value="">Sélectionner...</option>
                                <option value="global">Synthèse université</option>
                                <option value="formation">Par formation</option>
                                <option value="enseignant">Par enseignant</option>
                            </select>
                        </div>
                        <div class="mb-3" id="blockFormation" style="display:none;">
                            <label class="form-label fw-semibold">Formation</label>
                            <select name="formation_id" class="form-select">
                                <option>L3 Informatique</option>
                                <option>M1 Mathématiques</option>
                                <option>M2 IA</option>
                                <option>M1 Data Science</option>
                            </select>
                        </div>
                        <div class="mb-3" id="blockEnseignant" style="display:none;">
                            <label class="form-label fw-semibold">Enseignant</label>
                            <select name="enseignant_id" class="form-select">
                                <option>Jean Martin</option>
                                <option>Claire Moreau</option>
                                <option>Sophie Garnier</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Période <span class="text-danger">*</span></label>
                            <select name="periode_id" class="form-select" required>
                                <option value="">Toutes les périodes</option>
                                <option value="2">S2 2025-2026 (en cours)</option>
                                <option value="1">S1 2025-2026</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Période de début</label>
                            <input type="date" name="date_debut" class="form-control" value="{{ date('Y-m-d', strtotime('-6 months')) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Période de fin</label>
                            <input type="date" name="date_fin" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="lni lni-download me-1"></i>Générer le PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Rapports disponibles</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Rapport</th>
                                    <th>Type</th>
                                    <th>Période</th>
                                    <th>Date génération</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="font-w500">Synthèse Paris-Saclay S2 2026</span><small class="d-block text-muted">634 Ko</small></td>
                                    <td><span class="badge badge-sm badge-primary">Global</span></td>
                                    <td class="fs-13">Fév – Mai 2026</td>
                                    <td class="text-muted fs-13">19/05/2026</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-xs btn-outline-primary me-1"><i class="lni lni-download"></i></a>
                                        <button class="btn btn-xs btn-outline-danger"><i class="lni lni-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="font-w500">Rapport J. Martin – Algo avancée</span><small class="d-block text-muted">280 Ko</small></td>
                                    <td><span class="badge badge-sm badge-info">Enseignant</span></td>
                                    <td class="fs-13">S1 2025-2026</td>
                                    <td class="text-muted fs-13">25/01/2026</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-xs btn-outline-primary me-1"><i class="lni lni-download"></i></a>
                                        <button class="btn btn-xs btn-outline-danger"><i class="lni lni-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="font-w500">Rapport M2 IA – S1 2025-2026</span><small class="d-block text-muted">412 Ko</small></td>
                                    <td><span class="badge badge-sm badge-warning">Formation</span></td>
                                    <td class="fs-13">Sep 2025 – Jan 2026</td>
                                    <td class="text-muted fs-13">22/01/2026</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-xs btn-outline-primary me-1"><i class="lni lni-download"></i></a>
                                        <button class="btn btn-xs btn-outline-danger"><i class="lni lni-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Stats rapides --}}
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body py-3">
                            <h3 class="text-primary font-w700">4.1</h3>
                            <p class="mb-0 fs-13 text-muted">Note moyenne / 5</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body py-3">
                            <h3 class="text-success font-w700">85%</h3>
                            <p class="mb-0 fs-13 text-muted">Taux de participation</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body py-3">
                            <h3 class="text-warning font-w700">412</h3>
                            <p class="mb-0 fs-13 text-muted">Évaluations reçues</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('typeRapport').addEventListener('change', function () {
        document.getElementById('blockFormation').style.display  = this.value === 'formation'   ? 'block' : 'none';
        document.getElementById('blockEnseignant').style.display = this.value === 'enseignant'  ? 'block' : 'none';
    });
</script>
@endpush
