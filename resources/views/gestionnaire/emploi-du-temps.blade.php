@extends('layouts.app')

@section('title', 'Emploi du temps — Semaine du ' . $emploi->semaine->format('d/m/Y'))

@section('content')
<div class="container-fluid">

    {{-- En-tête --}}
    <div class="form-head d-flex mb-3 align-items-start flex-wrap gap-2">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">
                Semaine du {{ $emploi->semaine->translatedFormat('d MMMM Y') }}
            </h2>
            <p class="mb-0">{{ $annexe->nom }}</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            @if($emploi->isPublie())
                <span class="badge badge-success px-3 py-2 fs-13">Publié</span>
            @else
                <span class="badge badge-secondary px-3 py-2 fs-13">Brouillon</span>
            @endif
            <form method="POST" action="{{ route('gestionnaire.emplois-du-temps.publier', $emploi->id) }}">
                @csrf
                <button type="submit" class="btn btn-sm {{ $emploi->isPublie() ? 'btn-outline-warning' : 'btn-success' }}">
                    <i class="lni {{ $emploi->isPublie() ? 'lni-eye-alt' : 'lni-checkmark' }} me-1"></i>
                    {{ $emploi->isPublie() ? 'Dépublier' : 'Publier' }}
                </button>
            </form>
            <a href="{{ route('gestionnaire.emplois-du-temps') }}" class="btn btn-outline-secondary btn-sm">
                <i class="lni lni-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Grille par jour --}}
    <div class="row g-3">
        @foreach($jours as $num => $jour)
        <div class="col-xl-4 col-lg-6 col-12">
            <div class="card h-100">
                <div class="card-header border-0 d-flex align-items-center justify-content-between py-3">
                    <div>
                        <h5 class="mb-0 font-w600">{{ $jour['label'] }}</h5>
                        <span class="text-muted fs-12">{{ $jour['date']->translatedFormat('d MMMM') }}</span>
                    </div>
                    <button class="btn btn-xs btn-outline-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#modalCreneau"
                        data-jour="{{ $num }}"
                        data-jour-label="{{ $jour['label'] }}">
                        <i class="lni lni-plus"></i>
                    </button>
                </div>
                <div class="card-body p-0">
                    @if($jour['creneaux']->isEmpty())
                        <div class="text-center py-4 text-muted fs-13">
                            <i class="lni lni-calendar d-block mb-1"></i>
                            Aucun créneau
                        </div>
                    @else
                        @foreach($jour['creneaux'] as $c)
                        <div class="d-flex align-items-start px-3 py-2 border-bottom gap-3">
                            <div class="text-muted fs-12 text-nowrap" style="min-width:80px;">
                                {{ substr($c->heure_debut, 0, 5) }} – {{ substr($c->heure_fin, 0, 5) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-1 mb-1">
                                    <span class="badge badge-sm {{ \App\Models\Creneau::typeColor($c->type_cours) }}">
                                        {{ strtoupper($c->type_cours) }}
                                    </span>
                                    <span class="font-w500 fs-13">{{ $c->matiere }}</span>
                                </div>
                                @if($c->enseignant)
                                    <div class="text-muted fs-12">
                                        <i class="lni lni-user me-1"></i>{{ $c->enseignant->prenom }} {{ $c->enseignant->nom }}
                                    </div>
                                @endif
                                @if($c->salle)
                                    <div class="text-muted fs-12">
                                        <i class="lni lni-map-marker me-1"></i>{{ $c->salle }}
                                    </div>
                                @endif
                            </div>
                            <form method="POST"
                                action="{{ route('gestionnaire.creneaux.supprimer', $c->id) }}"
                                onsubmit="return confirm('Supprimer ce créneau ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-outline-danger border-0 p-1">
                                    <i class="lni lni-trash"></i>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>

{{-- Modal : Ajouter un créneau --}}
<div class="modal fade" id="modalCreneau" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un créneau — <span id="modalJourLabel"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('gestionnaire.emplois-du-temps.creneaux.creer', $emploi->id) }}">
                @csrf
                <input type="hidden" name="jour" id="inputJour">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Début <span class="text-danger">*</span></label>
                            <input type="time" name="heure_debut" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Fin <span class="text-danger">*</span></label>
                            <input type="time" name="heure_fin" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Matière <span class="text-danger">*</span></label>
                            <input type="text" name="matiere" class="form-control" placeholder="ex: Mathématiques" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Type</label>
                            <select name="type_cours" class="form-control">
                                <option value="cours">Cours</option>
                                <option value="td">TD</option>
                                <option value="tp">TP</option>
                                <option value="examen">Examen</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Salle</label>
                            <input type="text" name="salle" class="form-control" placeholder="ex: A201">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Enseignant</label>
                            <select name="enseignant_id" class="form-control">
                                <option value="">— Aucun —</option>
                                @foreach($enseignants as $e)
                                    <option value="{{ $e->id }}">{{ $e->prenom }} {{ $e->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="lni lni-plus me-1"></i>Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-bs-target="#modalCreneau"]');
    if (!btn) return;
    document.getElementById('inputJour').value    = btn.dataset.jour;
    document.getElementById('modalJourLabel').textContent = btn.dataset.jourLabel;
});
</script>
@endpush
