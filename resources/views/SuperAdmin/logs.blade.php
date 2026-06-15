@extends('layouts.superadmin')

@section('title', "Logs d'audit")

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Logs d'audit</h2>
            <p class="mb-0">Historique des connexions et actions importantes</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary">
                <i class="lni lni-download me-1"></i>Exporter CSV
            </button>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap gap-2">
                <input type="text" class="form-control w-auto" id="searchLog" placeholder="Rechercher un utilisateur...">
                <select class="form-select w-auto" id="filterAction">
                    <option value="">Toutes les actions</option>
                    <option value="connexion">Connexion</option>
                    <option value="deconnexion">Déconnexion</option>
                    <option value="evaluation">Évaluation soumise</option>
                    <option value="inscription_approuvee">Inscription approuvée</option>
                    <option value="inscription_rejetee">Inscription rejetée</option>
                    <option value="rapport_exporte">Rapport exporté</option>
                    <option value="critere_modifie">Critère modifié</option>
                    <option value="universite_creee">Université créée</option>
                </select>
                <select class="form-select w-auto" id="filterNiveau">
                    <option value="">Tous les niveaux</option>
                    <option value="info">Info</option>
                    <option value="warning">Avertissement</option>
                    <option value="error">Erreur</option>
                </select>
                <input type="date" class="form-control w-auto" id="filterDateDebut">
                <input type="date" class="form-control w-auto" id="filterDateFin">
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tableLogs">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Action</th>
                            <th>Ressource / Détail</th>
                            <th>Adresse IP</th>
                            <th>Date / Heure</th>
                            <th class="text-center">Niveau</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="font-w500">Marie Dupont</span><small class="d-block text-muted">Étudiant · Paris-Saclay</small></td>
                            <td>Évaluation soumise</td>
                            <td class="text-muted fs-13">Enseignant : J. Martin – Algo avancé</td>
                            <td class="text-muted fs-12">192.168.1.45</td>
                            <td class="fs-13">19/05/2026 09:14:32</td>
                            <td class="text-center"><span class="badge badge-xs badge-primary">Info</span></td>
                        </tr>
                        <tr>
                            <td><span class="font-w500">SuperAdmin</span><small class="d-block text-muted">Administrateur système</small></td>
                            <td>Inscription approuvée</td>
                            <td class="text-muted fs-13">Utilisateur #247 : Jean Martin (ENS Lyon)</td>
                            <td class="text-muted fs-12">10.0.0.1</td>
                            <td class="fs-13">19/05/2026 08:55:10</td>
                            <td class="text-center"><span class="badge badge-xs badge-success">Info</span></td>
                        </tr>
                        <tr>
                            <td><span class="font-w500">Sophie Leroy</span><small class="d-block text-muted">Responsable · Sorbonne</small></td>
                            <td>Connexion</td>
                            <td class="text-muted fs-13">Tableau de bord responsable</td>
                            <td class="text-muted fs-12">195.176.32.11</td>
                            <td class="fs-13">19/05/2026 08:32:05</td>
                            <td class="text-center"><span class="badge badge-xs badge-primary">Info</span></td>
                        </tr>
                        <tr>
                            <td><span class="font-w500">SuperAdmin</span><small class="d-block text-muted">Administrateur système</small></td>
                            <td>Rapport exporté</td>
                            <td class="text-muted fs-13">Synthèse globale S2 2025-2026</td>
                            <td class="text-muted fs-12">10.0.0.1</td>
                            <td class="fs-13">19/05/2026 08:15:44</td>
                            <td class="text-center"><span class="badge badge-xs badge-primary">Info</span></td>
                        </tr>
                        <tr>
                            <td><span class="font-w500">Utilisateur inconnu</span><small class="d-block text-muted">—</small></td>
                            <td>Tentative connexion échouée</td>
                            <td class="text-muted fs-13">Email : admin@fake.com (3 tentatives)</td>
                            <td class="text-muted fs-12">185.234.19.82</td>
                            <td class="fs-13">18/05/2026 23:42:17</td>
                            <td class="text-center"><span class="badge badge-xs badge-danger">Erreur</span></td>
                        </tr>
                        <tr>
                            <td><span class="font-w500">Claire Moreau</span><small class="d-block text-muted">Enseignant · Paris-Saclay</small></td>
                            <td>Connexion</td>
                            <td class="text-muted fs-13">Tableau de bord enseignant</td>
                            <td class="text-muted fs-12">192.168.1.102</td>
                            <td class="fs-13">18/05/2026 11:20:08</td>
                            <td class="text-center"><span class="badge badge-xs badge-primary">Info</span></td>
                        </tr>
                        <tr>
                            <td><span class="font-w500">SuperAdmin</span><small class="d-block text-muted">Administrateur système</small></td>
                            <td>Critère modifié</td>
                            <td class="text-muted fs-13">Pondération « Pédagogie » : 25% → 30%</td>
                            <td class="text-muted fs-12">10.0.0.1</td>
                            <td class="fs-13">18/05/2026 10:05:30</td>
                            <td class="text-center"><span class="badge badge-xs badge-warning">Avert.</span></td>
                        </tr>
                        <tr>
                            <td><span class="font-w500">Paul Bernard</span><small class="d-block text-muted">Étudiant · Bordeaux</small></td>
                            <td>Compte suspendu</td>
                            <td class="text-muted fs-13">Suspension par SuperAdmin – comportement abusif</td>
                            <td class="text-muted fs-12">10.0.0.1</td>
                            <td class="fs-13">17/05/2026 16:30:00</td>
                            <td class="text-center"><span class="badge badge-xs badge-warning">Avert.</span></td>
                        </tr>
                        <tr>
                            <td><span class="font-w500">SuperAdmin</span><small class="d-block text-muted">Administrateur système</small></td>
                            <td>Université créée</td>
                            <td class="text-muted fs-13">Université de Genève (UNIGE) ajoutée</td>
                            <td class="text-muted fs-12">10.0.0.1</td>
                            <td class="fs-13">15/05/2026 14:12:55</td>
                            <td class="text-center"><span class="badge badge-xs badge-primary">Info</span></td>
                        </tr>
                        <tr>
                            <td><span class="font-w500">Amina Chérif</span><small class="d-block text-muted">Enseignant · ENS Paris</small></td>
                            <td>Connexion</td>
                            <td class="text-muted fs-13">Première connexion après inscription</td>
                            <td class="text-muted fs-12">78.192.45.33</td>
                            <td class="fs-13">14/05/2026 09:00:11</td>
                            <td class="text-center"><span class="badge badge-xs badge-success">Info</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="text-muted fs-13">Affichage 1-10 sur 1 284 entrées</span>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#">Précédent</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><span class="page-link">...</span></li>
                        <li class="page-item"><a class="page-link" href="#">129</a></li>
                        <li class="page-item"><a class="page-link" href="#">Suivant</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.getElementById('searchLog').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#tableLogs tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    document.getElementById('filterAction').addEventListener('change', function () {
        // TODO: server-side filtering
    });

    document.getElementById('filterNiveau').addEventListener('change', function () {
        const val = this.value;
        document.querySelectorAll('#tableLogs tbody tr').forEach(row => {
            if (!val) { row.style.display = ''; return; }
            const badge = row.querySelector('.badge')?.textContent.toLowerCase() || '';
            row.style.display = badge.includes(val === 'info' ? 'info' : val === 'warning' ? 'avert' : 'err') ? '' : 'none';
        });
    });
</script>
@endpush
