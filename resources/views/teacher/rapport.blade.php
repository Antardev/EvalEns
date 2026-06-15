@extends('layouts.app')

@section('title', 'Mon rapport PDF')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Mon rapport PDF</h2>
            <p class="mb-0">Exporter vos résultats d'évaluation en PDF</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-5 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Générer un rapport</h4>
                    <small class="text-muted">Le rapport contient uniquement vos statistiques agrégées</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('teacher.rapport.exporter') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Période <span class="text-danger">*</span></label>
                            <select name="periode_id" class="form-select" required>
                                <option value="">Sélectionner une période...</option>
                                <option value="2">S2 2025-2026 (en cours)</option>
                                <option value="1">S1 2025-2026</option>
                                <option value="3">S2 2024-2025</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Contenu à inclure</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="sections[]" value="global" id="secGlobal" checked>
                                <label class="form-check-label" for="secGlobal">Résumé global et KPIs</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="sections[]" value="criteres" id="secCriteres" checked>
                                <label class="form-check-label" for="secCriteres">Score par critère (graphiques)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="sections[]" value="ues" id="secUEs" checked>
                                <label class="form-check-label" for="secUEs">Détail par UE</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="sections[]" value="evolution" id="secEvol">
                                <label class="form-check-label" for="secEvol">Évolution sur les semestres précédents</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="sections[]" value="commentaires" id="secComm">
                                <label class="form-check-label" for="secComm">Commentaires anonymisés</label>
                            </div>
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

        <div class="col-xl-7 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Mes rapports générés</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Rapport</th>
                                    <th>Période</th>
                                    <th>Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="font-w500">Rapport personnel S1 2025-26</span><small class="d-block text-muted">245 Ko</small></td>
                                    <td class="fs-13">S1 2025-2026</td>
                                    <td class="text-muted fs-13">25/01/2026</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-xs btn-outline-primary"><i class="lni lni-download"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="font-w500">Rapport personnel S2 2024-25</span><small class="d-block text-muted">218 Ko</small></td>
                                    <td class="fs-13">S2 2024-2025</td>
                                    <td class="text-muted fs-13">30/06/2025</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-xs btn-outline-primary"><i class="lni lni-download"></i></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Informations importantes</h5>
                    <ul class="fs-13 text-muted list-unstyled">
                        <li class="mb-2"><i class="lni lni-information me-2 text-primary"></i>Le rapport ne contient que des <strong>données agrégées</strong>. Aucune identité étudiant n'y figure.</li>
                        <li class="mb-2"><i class="lni lni-information me-2 text-primary"></i>Les résultats ne sont disponibles que si <strong>au moins 5 étudiants</strong> ont répondu.</li>
                        <li class="mb-2"><i class="lni lni-information me-2 text-primary"></i>Le rapport est destiné à votre usage personnel et à vos entretiens avec le responsable pédagogique.</li>
                        <li><i class="lni lni-information me-2 text-primary"></i>Les rapports sont conservés pendant <strong>3 ans</strong>.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
