@extends('layouts.superadmin')

@section('title', 'Référentiel des universités')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Référentiel des universités</h2>
            <p class="mb-0">Liste des établissements disponibles pour l'inscription des directeurs</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ═══════════════ Onglets ═══════════════ --}}
    <ul class="nav nav-tabs mb-3" id="tabsUniversites" role="tablist">
        {{-- 🔒 Onglet Référentiel (désactivé)
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-referentiel-tab"
                    data-bs-toggle="tab" data-bs-target="#tab-referentiel"
                    type="button" role="tab">
                <i class="lni lni-library me-1"></i>Référentiel
            </button>
        </li>
        --}}

        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-approuvees-tab"
                    data-bs-toggle="tab" data-bs-target="#tab-approuvees"
                    type="button" role="tab">
                <i class="lni lni-checkmark-circle me-1"></i>Universités approuvées
                <span class="badge bg-success ms-1">{{ $universitesApprouvees->total() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content">

        {{-- ═══════════════════════════════════════════════════════
             Onglet 2 : Universités approuvées
        ════════════════════════════════════════════════════════ --}}
        <div class="tab-pane fade show active" id="tab-approuvees" role="tabpanel">

            {{-- Barre de recherche + compteur --}}
            <div class="card mb-3">
                <div class="card-body py-3">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <input type="text" class="form-control w-auto" id="searchApprouvees"
                            placeholder="Rechercher par nom, acronyme ou directeur…" style="min-width:300px;">
                        <span class="text-muted fs-13 ms-auto" id="compteurApprouvees">
                            {{ $universitesApprouvees->total() }} université{{ $universitesApprouvees->total() !== 1 ? 's' : '' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Tableau --}}
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="tableApprouvees">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:50px">#</th>
                                    <th>Nom de l'université</th>
                                    <th style="width:160px">Acronyme</th>
                                    <th>Directeur approuvé</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($universitesApprouvees as $u)
                                <tr>
                                    {{-- Numérotation continue entre les pages --}}
                                    <td class="text-muted fs-13">
                                        {{ $universitesApprouvees->firstItem() + $loop->index }}
                                    </td>
                                    <td><span class="font-w500">{{ $u->nom }}</span></td>
                                    <td>
                                        @if($u->acronyme)
                                            <code>{{ $u->acronyme }}</code>
                                        @else
                                            <span class="text-muted fs-13">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($u->directeur)
                                            <i class="lni lni-user me-1 text-primary"></i>
                                            <span class="font-w500">
                                                {{ trim(($u->directeur->prenom ?? '') . ' ' . ($u->directeur->nom ?? $u->directeur->name)) }}
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ $u->directeur->email }}</small>
                                        @else
                                            <span class="text-muted fs-13">Aucun directeur</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr id="rowVideApprouvees">
                                    <td colspan="4" class="text-center text-muted py-5">
                                        <i class="lni lni-library fs-2 d-block mb-2"></i>
                                        Aucune université approuvée pour le moment.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- ═══════════ PAGINATION ═══════════ --}}
                    @if($universitesApprouvees->total() > 0)
                    <div class="px-4 py-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <span class="text-muted fs-13">
                            Affichage {{ $universitesApprouvees->firstItem() ?? 0 }}–{{ $universitesApprouvees->lastItem() ?? 0 }}
                            sur {{ $universitesApprouvees->total() }} entrée{{ $universitesApprouvees->total() !== 1 ? 's' : '' }}
                        </span>
                        {{ $universitesApprouvees->links('pagination::bootstrap-5') }}
                    </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
(function () {
    /* ── Recherche côté client : Universités approuvées ── */
    const searchApprouvees   = document.getElementById('searchApprouvees');
    const compteurApprouvees = document.getElementById('compteurApprouvees');
    const rowsApprouvees     = document.querySelectorAll('#tableApprouvees tbody tr:not(#rowVideApprouvees)');

    if (searchApprouvees) {
        searchApprouvees.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            let visible = 0;
            rowsApprouvees.forEach(function (row) {
                const match = row.textContent.toLowerCase().includes(q);
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            if (compteurApprouvees) {
                compteurApprouvees.textContent = visible + ' université' + (visible !== 1 ? 's' : '');
            }
        });
    }
})();
</script>
@endpush
