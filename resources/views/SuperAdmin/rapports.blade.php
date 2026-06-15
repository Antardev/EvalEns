@extends('layouts.superadmin')

@section('title', 'Rapports PDF')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Rapports PDF</h2>
            <p class="mb-0">Générer et télécharger des rapports consolidés</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">

        {{-- Formulaire de génération --}}
        <div class="col-xl-4 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Générer un rapport</h4>
                    <small class="text-muted">Configurez les paramètres du rapport</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('superadmin.rapports.exporter') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Type de rapport <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required id="typeRapport">
                                <option value="">Sélectionner...</option>
                                <option value="global">Synthèse globale</option>
                                <option value="universite">Par université</option>
                                <option value="enseignant">Par enseignant</option>
                            </select>
                        </div>

                        <div class="mb-3" id="blockUniversite" style="display:none;">
                            <label class="form-label fw-semibold">Université</label>
                            <select name="universite_id" class="form-select">
                                <option value="">Toutes les universités</option>
                                <option value="1">Univ. Paris-Saclay</option>
                                <option value="2">Sorbonne Université</option>
                                <option value="3">ENS Lyon</option>
                                <option value="4">Univ. Bordeaux</option>
                                <option value="5">Univ. de Rennes</option>
                                <option value="6">Univ. Strasbourg</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Période de début <span class="text-danger">*</span></label>
                            <input type="date" name="date_debut" class="form-control" required
                                value="{{ date('Y-m-d', strtotime('-6 months')) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Période de fin <span class="text-danger">*</span></label>
                            <input type="date" name="date_fin" class="form-control" required
                                value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Inclure dans le rapport</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="options[]" value="graphiques" id="optGraphiques" checked>
                                <label class="form-check-label" for="optGraphiques">Graphiques & visualisations</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="options[]" value="commentaires" id="optCommentaires" checked>
                                <label class="form-check-label" for="optCommentaires">Commentaires anonymisés</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="options[]" value="comparaison" id="optComparaison">
                                <label class="form-check-label" for="optComparaison">Comparaison inter-universités</label>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="me-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                </svg>
                                Générer le PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Rapports récents --}}
        <div class="col-xl-8 col-lg-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Rapports générés</h4>
                    <small class="text-muted">Historique des rapports disponibles au téléchargement</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nom du rapport</th>
                                    <th>Type</th>
                                    <th>Période</th>
                                    <th>Généré par</th>
                                    <th>Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <span class="font-w500">Synthèse globale S2 2025-2026</span>
                                        <small class="d-block text-muted">842 Ko</small>
                                    </td>
                                    <td><span class="badge badge-sm badge-primary">Global</span></td>
                                    <td class="fs-13">Jan 2026 – Mai 2026</td>
                                    <td class="fs-13">SuperAdmin</td>
                                    <td class="text-muted fs-13">19/05/2026 08:15</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-xs btn-outline-primary me-1" title="Télécharger">
                                            <i class="lni lni-download"></i>
                                        </a>
                                        <button class="btn btn-xs btn-outline-danger" title="Supprimer">
                                            <i class="lni lni-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="font-w500">Rapport Paris-Saclay – Avr 2026</span>
                                        <small class="d-block text-muted">531 Ko</small>
                                    </td>
                                    <td><span class="badge badge-sm badge-info">Université</span></td>
                                    <td class="fs-13">Sep 2025 – Avr 2026</td>
                                    <td class="fs-13">SuperAdmin</td>
                                    <td class="text-muted fs-13">30/04/2026 14:22</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-xs btn-outline-primary me-1" title="Télécharger">
                                            <i class="lni lni-download"></i>
                                        </a>
                                        <button class="btn btn-xs btn-outline-danger" title="Supprimer">
                                            <i class="lni lni-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="font-w500">Synthèse globale S1 2025-2026</span>
                                        <small class="d-block text-muted">796 Ko</small>
                                    </td>
                                    <td><span class="badge badge-sm badge-primary">Global</span></td>
                                    <td class="fs-13">Sep 2025 – Jan 2026</td>
                                    <td class="fs-13">SuperAdmin</td>
                                    <td class="text-muted fs-13">20/01/2026 10:05</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-xs btn-outline-primary me-1" title="Télécharger">
                                            <i class="lni lni-download"></i>
                                        </a>
                                        <button class="btn btn-xs btn-outline-danger" title="Supprimer">
                                            <i class="lni lni-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="font-w500">Rapport ENS Lyon – Déc 2025</span>
                                        <small class="d-block text-muted">412 Ko</small>
                                    </td>
                                    <td><span class="badge badge-sm badge-info">Université</span></td>
                                    <td class="fs-13">Sep 2025 – Déc 2025</td>
                                    <td class="fs-13">SuperAdmin</td>
                                    <td class="text-muted fs-13">10/12/2025 09:30</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-xs btn-outline-primary me-1" title="Télécharger">
                                            <i class="lni lni-download"></i>
                                        </a>
                                        <button class="btn btn-xs btn-outline-danger" title="Supprimer">
                                            <i class="lni lni-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="font-w500">Synthèse globale 2024-2025</span>
                                        <small class="d-block text-muted">1.2 Mo</small>
                                    </td>
                                    <td><span class="badge badge-sm badge-primary">Global</span></td>
                                    <td class="fs-13">Sep 2024 – Juin 2025</td>
                                    <td class="fs-13">SuperAdmin</td>
                                    <td class="text-muted fs-13">30/06/2025 11:00</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-xs btn-outline-primary me-1" title="Télécharger">
                                            <i class="lni lni-download"></i>
                                        </a>
                                        <button class="btn btn-xs btn-outline-danger" title="Supprimer">
                                            <i class="lni lni-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="text-muted fs-13">5 rapports disponibles</span>
                        <span class="text-muted fs-13">Espace utilisé : 3.8 Mo</span>
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
        const block = document.getElementById('blockUniversite');
        block.style.display = this.value === 'universite' ? 'block' : 'none';
    });
</script>
@endpush
