@extends('layouts.superadmin')

@section('title', "Logs d'audit")

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Logs d'audit</h2>
            <p class="mb-0">Historique des actions effectuées sur la plateforme</p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-secondary fs-13 d-flex align-items-center px-3">
                {{ number_format($total) }} entrée{{ $total > 1 ? 's' : '' }}
            </span>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('superadmin.logs') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label mb-1 fs-13">Rechercher un utilisateur</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Nom, prénom ou email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1 fs-13">Action</label>
                    <select name="action" class="form-select form-select-sm">
                        <option value="">Toutes</option>
                        @foreach(\App\Models\AuditLog::actionLabels() as $key => $label)
                            <option value="{{ $key }}" {{ request('action') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1 fs-13">Niveau</label>
                    <select name="niveau" class="form-select form-select-sm">
                        <option value="">Tous</option>
                        <option value="info"    {{ request('niveau') === 'info'    ? 'selected' : '' }}>Info</option>
                        <option value="warning" {{ request('niveau') === 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="error"   {{ request('niveau') === 'error'   ? 'selected' : '' }}>Erreur</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1 fs-13">Du</label>
                    <input type="date" name="date_debut" class="form-control form-control-sm"
                        value="{{ request('date_debut') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1 fs-13">Au</label>
                    <input type="date" name="date_fin" class="form-control form-control-sm"
                        value="{{ request('date_fin') }}">
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="lni lni-search-alt"></i>
                    </button>
                    @if(request()->hasAny(['search','action','niveau','date_debut','date_fin']))
                    <a href="{{ route('superadmin.logs') }}" class="btn btn-outline-secondary btn-sm" title="Réinitialiser">
                        <i class="lni lni-close"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Utilisateur</th>
                            <th>Action</th>
                            <th>Détails</th>
                            <th>IP</th>
                            <th>Date</th>
                            <th class="text-center">Niveau</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-4">
                                @if($log->user && $log->user->id)
                                    @php $initiale = strtoupper(substr($log->user->prenom ?? $log->user->name, 0, 1)); @endphp
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-circle"
                                            style="width:32px;height:32px;font-size:12px;font-weight:600;flex-shrink:0;">
                                            {{ $initiale }}
                                        </div>
                                        <div>
                                            <div class="fw-500 fs-14">{{ $log->getNomUtilisateur() }}</div>
                                            <small class="text-muted">{{ $log->user->role ?? '' }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted fs-13">
                                        <i class="lni lni-cog me-1"></i>Système
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-500 fs-13">{{ $log->getLabelAction() }}</span>
                            </td>
                            <td class="fs-13 text-muted" style="max-width:280px;">
                                {{ $log->details ?? '—' }}
                            </td>
                            <td class="fs-13 text-muted font-monospace">{{ $log->ip_address ?? '—' }}</td>
                            <td class="fs-13 text-muted" title="{{ $log->created_at->format('d/m/Y H:i:s') }}">
                                {{ $log->created_at->diffForHumans() }}
                            </td>
                            <td class="text-center">
                                @if($log->niveau === 'error')
                                    <span class="badge badge-sm badge-danger">Erreur</span>
                                @elseif($log->niveau === 'warning')
                                    <span class="badge badge-sm badge-warning">Warning</span>
                                @else
                                    <span class="badge badge-sm badge-info">Info</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="lni lni-files d-block mb-2" style="font-size:32px;opacity:.3;"></i>
                                <span class="fs-14">Aucun log trouvé</span>
                                @if(request()->hasAny(['search','action','niveau','date_debut','date_fin']))
                                    <br><a href="{{ route('superadmin.logs') }}" class="fs-13 text-primary mt-1 d-inline-block">Réinitialiser les filtres</a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                <small class="text-muted">
                    Entrées {{ $logs->firstItem() }}–{{ $logs->lastItem() }} sur {{ number_format($logs->total()) }}
                </small>
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
