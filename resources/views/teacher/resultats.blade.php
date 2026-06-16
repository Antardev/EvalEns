@extends('layouts.app')

@section('title', 'Mes résultats')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Mes résultats</h2>
            <p class="mb-0">Statistiques agrégées et anonymisées par questionnaire</p>
        </div>
        <a href="{{ route('teacher.commentaires') }}" class="btn btn-outline-secondary btn-sm">
            <i class="lni lni-comments-alt me-1"></i>Voir les commentaires
        </a>
    </div>

    <div class="alert alert-info fs-13 mb-4">
        <i class="lni lni-lock-alt me-2"></i>
        Ces résultats sont des <strong>statistiques agrégées et anonymisées</strong>. Vos étudiants ne sont pas identifiables.
    </div>

    @if($liens->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="lni lni-clipboard fs-1 d-block mb-3"></i>
                Aucun questionnaire ne vous a encore été assigné.
            </div>
        </div>
    @else

    @foreach($liens as $lien)
    @php
        $stats    = $statsParLien[$lien->id];
        $moy      = $stats['moyenne'];
        $criteres = $stats['parCritere'];
        $pct      = $moy ? round($moy * 20) : null;
        $color    = $pct >= 80 ? 'success' : ($pct >= 60 ? 'primary' : ($pct >= 40 ? 'warning' : 'danger'));
    @endphp
    <div class="card mb-4">
        <div class="card-header border-0 pb-0 d-flex align-items-center justify-content-between">
            <div>
                <h5 class="font-w600 mb-1">{{ $lien->titre }}</h5>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge badge-primary">{{ $lien->classe }}</span>
                    @if($lien->matiere)
                        <span class="badge badge-info">{{ $lien->matiere }}</span>
                    @endif
                    @if($lien->statut === 'actif')
                        <span class="badge badge-success">Actif</span>
                    @else
                        <span class="badge badge-secondary">Fermé</span>
                    @endif
                </div>
            </div>
            <div class="text-end">
                <div class="fs-24 font-w700 text-{{ $pct !== null ? $color : 'muted' }}">
                    {{ $pct !== null ? $pct.'%' : '—' }}
                </div>
                <small class="text-muted">{{ $lien->reponses_count }} réponse{{ $lien->reponses_count !== 1 ? 's' : '' }}</small>
            </div>
        </div>

        <div class="card-body">
            @if($lien->reponses_count === 0)
                <p class="text-muted fs-13 mb-0">Aucune réponse pour ce questionnaire.</p>
            @else
                @if(!empty($criteres))
                <div class="row">
                    @foreach($criteres as $label => $score)
                    @php $scorePct = round($score * 20); @endphp
                    <div class="col-xl-6 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fs-13">{{ $label }}</span>
                            <span class="fs-13 font-w600 text-{{ $scorePct >= 80 ? 'success' : ($scorePct >= 60 ? 'primary' : ($scorePct >= 40 ? 'warning' : 'danger')) }}">
                                {{ $scorePct }}%
                            </span>
                        </div>
                        <div class="progress" style="height:7px;">
                            <div class="progress-bar bg-{{ $scorePct >= 80 ? 'success' : ($scorePct >= 60 ? 'primary' : ($scorePct >= 40 ? 'warning' : 'danger')) }}"
                                style="width:{{ $scorePct }}%; transition:.6s;">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Commentaires liés à ce lien --}}
                @php $nbCommentaires = $lien->reponses->filter(fn($r) => trim($r->commentaire ?? '') !== '')->count(); @endphp
                @if($nbCommentaires > 0)
                <div class="mt-2 pt-2 border-top">
                    <a href="{{ route('teacher.commentaires', ['lien_id' => $lien->id]) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="lni lni-comments-alt me-1"></i>{{ $nbCommentaires }} commentaire{{ $nbCommentaires > 1 ? 's' : '' }} pour ce questionnaire
                    </a>
                </div>
                @endif
            @endif
        </div>
    </div>
    @endforeach

    @endif

</div>
@endsection
