@extends('layouts.app')

@section('title', 'Emplois du temps — ' . $annexe->nom)

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Emplois du temps</h2>
            <p class="mb-0">{{ $annexe->nom }}</p>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreer">
            <i class="lni lni-plus me-1"></i>Nouvelle semaine
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            @if($emplois->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="lni lni-calendar fs-1 d-block mb-3"></i>
                    Aucun emploi du temps créé. Cliquez sur « Nouvelle semaine » pour commencer.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Semaine</th>
                                <th>Créneaux</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($emplois as $emploi)
                            <tr>
                                <td class="font-w500">
                                    Semaine du {{ $emploi->semaine->translatedFormat('d MMMM Y') }}
                                    @if($emploi->semaine->isCurrentWeek())
                                        <span class="badge badge-info badge-sm ms-1">Cette semaine</span>
                                    @endif
                                </td>
                                <td class="text-muted fs-13">{{ $emploi->creneaux_count }} créneau{{ $emploi->creneaux_count !== 1 ? 'x' : '' }}</td>
                                <td>
                                    @if($emploi->isPublie())
                                        <span class="badge badge-success">Publié</span>
                                    @else
                                        <span class="badge badge-secondary">Brouillon</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('gestionnaire.emplois-du-temps.voir', $emploi->id) }}"
                                        class="btn btn-xs btn-outline-primary me-1">
                                        <i class="lni lni-pencil"></i> Éditer
                                    </a>
                                    <form method="POST" action="{{ route('gestionnaire.emplois-du-temps.publier', $emploi->id) }}"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-xs {{ $emploi->isPublie() ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                            <i class="lni {{ $emploi->isPublie() ? 'lni-eye-alt' : 'lni-checkmark' }}"></i>
                                            {{ $emploi->isPublie() ? 'Dépublier' : 'Publier' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('gestionnaire.emplois-du-temps.supprimer', $emploi->id) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('Supprimer cet emploi du temps et tous ses créneaux ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-outline-danger ms-1">
                                            <i class="lni lni-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>

{{-- Modal : Créer une semaine --}}
<div class="modal fade" id="modalCreer" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle semaine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('gestionnaire.emplois-du-temps.creer') }}">
                @csrf
                <div class="modal-body">
                    <label class="form-label fw-semibold">Choisir une date de la semaine <span class="text-danger">*</span></label>
                    <input type="date" name="semaine" class="form-control"
                        value="{{ now()->startOfWeek()->toDateString() }}" required>
                    <small class="text-muted fs-12 mt-1 d-block">
                        La date sera automatiquement ramenée au lundi de la semaine.
                    </small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-sm">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
