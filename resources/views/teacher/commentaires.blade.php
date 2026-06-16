@extends('layouts.app')

@section('title', 'Commentaires')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Commentaires anonymisés</h2>
            <p class="mb-0">Retours textuels de vos étudiants — identités masquées</p>
        </div>
        <a href="{{ route('teacher.resultats') }}" class="btn btn-outline-secondary btn-sm">
            <i class="lni lni-bar-chart me-1"></i>Voir les résultats
        </a>
    </div>

    <div class="alert alert-info fs-13 mb-4">
        <i class="lni lni-lock-alt me-2"></i>
        Les commentaires sont <strong>strictement anonymes</strong> — aucune information permettant d'identifier un étudiant n'est visible.
    </div>

    {{-- Filtre par questionnaire --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('teacher.commentaires') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <select name="lien_id" class="form-control w-auto">
                    <option value="">Tous les questionnaires</option>
                    @foreach($mesLiens as $l)
                        <option value="{{ $l->id }}" {{ request('lien_id') == $l->id ? 'selected' : '' }}>
                            {{ $l->titre }} — {{ $l->classe }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="lni lni-search-alt me-1"></i>Filtrer
                </button>
                @if(request('lien_id'))
                    <a href="{{ route('teacher.commentaires') }}" class="btn btn-outline-secondary">
                        <i class="lni lni-close me-1"></i>Effacer
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Liste des commentaires --}}
    @if($commentaires->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="lni lni-comments-alt fs-1 d-block mb-3"></i>
                Aucun commentaire {{ request('lien_id') ? 'pour ce questionnaire' : 'reçu pour le moment' }}.
            </div>
        </div>
    @else
        @foreach($commentaires as $rep)
        @php
            $scores   = collect($rep->scores);
            $moyenne  = $scores->isNotEmpty() ? round($scores->avg('score') * 20) : null;
            $color    = $moyenne >= 80 ? 'success' : ($moyenne >= 60 ? 'primary' : ($moyenne >= 40 ? 'warning' : 'danger'));
        @endphp
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="badge badge-primary">{{ $rep->lien->classe }}</span>
                        @if($rep->lien->matiere)
                            <span class="badge badge-info">{{ $rep->lien->matiere }}</span>
                        @endif
                        <span class="text-muted fs-12">{{ $rep->lien->titre }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        @if($moyenne !== null)
                            <span class="badge badge-{{ $color }} fs-12">{{ $moyenne }}%</span>
                        @endif
                        <small class="text-muted">
                            {{ $rep->soumis_at ? $rep->soumis_at->diffForHumans() : '—' }}
                        </small>
                    </div>
                </div>

                <p class="mb-0 fs-14 fst-italic text-dark">"{{ $rep->commentaire }}"</p>

                {{-- Scores détaillés (accordéon) --}}
                @if($scores->isNotEmpty())
                <div class="mt-3">
                    <button class="btn btn-sm btn-outline-secondary" type="button"
                        data-bs-toggle="collapse" data-bs-target="#scores-{{ $rep->id }}">
                        <i class="lni lni-bar-chart me-1"></i>Voir les scores
                    </button>
                    <div class="collapse mt-2" id="scores-{{ $rep->id }}">
                        <div class="row">
                            @foreach($rep->scores as $s)
                            @php $p = round($s['score'] * 20); @endphp
                            <div class="col-md-6 mb-2">
                                <div class="d-flex justify-content-between fs-12 mb-1">
                                    <span class="text-muted">{{ $s['label'] }}</span>
                                    <span class="font-w600 text-{{ $p >= 80 ? 'success' : ($p >= 60 ? 'primary' : ($p >= 40 ? 'warning' : 'danger')) }}">{{ $p }}%</span>
                                </div>
                                <div class="progress" style="height:5px;">
                                    <div class="progress-bar bg-{{ $p >= 80 ? 'success' : ($p >= 60 ? 'primary' : ($p >= 40 ? 'warning' : 'danger')) }}"
                                        style="width:{{ $p }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endforeach

        <div class="d-flex justify-content-between align-items-center mt-2">
            <span class="text-muted fs-13">{{ $commentaires->total() }} commentaire{{ $commentaires->total() > 1 ? 's' : '' }} au total</span>
            <div>{{ $commentaires->links() }}</div>
        </div>
    @endif

</div>
@endsection
