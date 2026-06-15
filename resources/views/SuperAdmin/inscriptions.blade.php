@extends('layouts.superadmin')

@section('title', 'Gestion des inscriptions')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Gestion des inscriptions</h2>
            <p class="mb-0">Valider les demandes d'inscription des universités</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header border-0 pb-0">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'attente' ? 'active' : '' }}"
                       href="{{ route('superadmin.inscriptions') }}">
                        Demandes en attente
                        @if($activeTab === 'attente')
                            <span class="badge badge-xs badge-warning ms-1">{{ $enAttente->count() }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'historique' ? 'active' : '' }}"
                       href="{{ route('superadmin.inscriptions.historique') }}">
                        Historique
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body">

            {{-- ===== Onglet EN ATTENTE ===== --}}
            @if($activeTab === 'attente')

                @if($enAttente->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="lni lni-checkmark-circle" style="font-size:48px;"></i>
                        <p class="mt-3 mb-0">Aucune demande en attente.</p>
                    </div>
                @else
                    <input type="text" class="form-control w-auto mb-3" id="searchInscriptions"
                        placeholder="Rechercher une université ou un directeur...">

                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="tableInscriptions">
                            <thead>
                                <tr>
                                    <th>Université</th>
                                    <th>Directeur</th>
                                    <th>Email directeur</th>
                                    <th>Soumis le</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($enAttente as $univ)
                                <tr>
                                    <td>
                                        <span class="font-w500">{{ $univ->nom }}</span>
                                        @if($univ->acronyme)
                                            <small class="text-muted ms-1">({{ $univ->acronyme }})</small>
                                        @endif
                                    </td>
                                    <td>{{ $univ->directeur->name }}</td>
                                    <td class="text-muted fs-13">{{ $univ->directeur->email }}</td>
                                    <td class="text-muted fs-13">{{ $univ->created_at->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-xs btn-success me-1"
                                            data-bs-toggle="modal" data-bs-target="#modalApprouver"
                                            data-id="{{ $univ->id }}" data-nom="{{ $univ->nom }}">
                                            <i class="lni lni-checkmark me-1"></i>Approuver
                                        </button>
                                        <button class="btn btn-xs btn-danger"
                                            data-bs-toggle="modal" data-bs-target="#modalRejeter"
                                            data-id="{{ $univ->id }}" data-nom="{{ $univ->nom }}">
                                            <i class="lni lni-close me-1"></i>Rejeter
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            {{-- ===== Onglet HISTORIQUE ===== --}}
            @else

                @if($historique->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="lni lni-archive" style="font-size:48px;"></i>
                        <p class="mt-3 mb-0">Aucun historique disponible.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Université</th>
                                    <th>Directeur</th>
                                    <th>Statut</th>
                                    <th>Traité le</th>
                                    <th>Par</th>
                                    <th>Motif</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($historique as $univ)
                                <tr>
                                    <td>
                                        <span class="font-w500">{{ $univ->nom }}</span>
                                        @if($univ->acronyme)
                                            <small class="text-muted ms-1">({{ $univ->acronyme }})</small>
                                        @endif
                                        <small class="d-block text-muted">{{ $univ->ville }}</small>
                                    </td>
                                    <td class="fs-13">{{ $univ->directeur->name }}<br>
                                        <small class="text-muted">{{ $univ->directeur->email }}</small>
                                    </td>
                                    <td>
                                        @if($univ->isActive())
                                            <span class="badge badge-sm badge-success">Approuvée</span>
                                        @else
                                            <span class="badge badge-sm badge-danger">Rejetée</span>
                                        @endif
                                    </td>
                                    <td class="text-muted fs-13">
                                        {{ $univ->validee_at?->format('d/m/Y') ?? '—' }}
                                    </td>
                                    <td class="fs-13">{{ $univ->validateur?->name ?? '—' }}</td>
                                    <td class="text-muted fs-13 fst-italic">
                                        {{ $univ->motif_rejet ?? '—' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            @endif

        </div>
    </div>

</div>

{{-- Modal : Approuver --}}
<div class="modal fade" id="modalApprouver" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer l'approbation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formApprouver" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Approuver l'inscription de <strong id="nomApprouver"></strong> ?</p>
                    <p class="text-muted fs-13 mb-0">
                        Le directeur pourra immédiatement accéder à son tableau de bord.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="lni lni-checkmark me-1"></i>Approuver
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal : Rejeter --}}
<div class="modal fade" id="modalRejeter" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rejeter la demande</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formRejeter" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Rejeter la demande de <strong id="nomRejeter"></strong>.</p>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">
                            Motif du rejet <span class="text-danger">*</span>
                        </label>
                        <textarea name="motif" class="form-control" rows="3" required
                            placeholder="Ce motif sera affiché au directeur..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="lni lni-close me-1"></i>Rejeter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-bs-target="#modalApprouver"]').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('nomApprouver').textContent = this.dataset.nom;
            document.getElementById('formApprouver').action =
                '/superadmin/inscriptions/' + this.dataset.id + '/approuver';
        });
    });

    document.querySelectorAll('[data-bs-target="#modalRejeter"]').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('nomRejeter').textContent = this.dataset.nom;
            document.getElementById('formRejeter').action =
                '/superadmin/inscriptions/' + this.dataset.id + '/rejeter';
        });
    });

    const search = document.getElementById('searchInscriptions');
    if (search) {
        search.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#tableInscriptions tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }
</script>
@endpush
