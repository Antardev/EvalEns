@extends('layouts.app')

@section('title', 'Commentaires anonymisés')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Commentaires anonymisés</h2>
            <p class="mb-0">Retours textuels de vos étudiants — identités masquées</p>
        </div>
        <select class="form-select form-select-sm w-auto">
            <option>S2 2025-2026 (en cours)</option>
            <option>S1 2025-2026</option>
        </select>
    </div>

    <div class="alert alert-info fs-13 mb-4">
        <i class="lni lni-lock-alt me-2"></i>
        Les commentaires ci-dessous sont <strong>strictement anonymisés</strong>.
        Toute information permettant d'identifier un étudiant a été retirée.
        Seuls les commentaires des périodes clôturées sont visibles.
    </div>

    {{-- Filtres --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap gap-2">
                <select class="form-select w-auto" id="filterUE">
                    <option value="">Toutes les UE</option>
                    <option>Algorithmique avancée</option>
                    <option>Deep Learning</option>
                    <option>Traitement du langage</option>
                </select>
                <select class="form-select w-auto" id="filterSentiment">
                    <option value="">Tous</option>
                    <option value="positif">Positifs</option>
                    <option value="negatif">Critiques</option>
                    <option value="neutre">Neutres</option>
                </select>
                <input type="text" class="form-control w-auto" id="searchComment" placeholder="Rechercher un mot-clé...">
            </div>
        </div>
    </div>

    @php
    $commentaires = [
        ['Algorithmique avancée', 'Les explications sont très claires, les exemples concrets aident vraiment à comprendre les concepts.', 'positif', 4.5, 'Il y a 3 jours'],
        ['Deep Learning', 'Le cours est intéressant mais le rythme est parfois trop rapide. Plus d\'exercices pratiques seraient appréciés.', 'neutre', 3.5, 'Il y a 5 jours'],
        ['Algorithmique avancée', 'Excellent enseignant, très disponible et pédagogue. Les TD sont bien structurés.', 'positif', 5.0, 'Il y a 1 semaine'],
        ['Traitement du langage', 'Contenu très riche et à jour avec les dernières avancées du domaine. Cours stimulant intellectuellement.', 'positif', 4.8, 'Il y a 1 semaine'],
        ['Deep Learning', 'Manque parfois de clarté sur les objectifs de chaque séance. Serait bien d\'avoir un plan détaillé en début de cours.', 'negatif', 2.5, 'Il y a 2 semaines'],
        ['Algorithmique avancée', 'Les corrections de TD arrivent souvent en retard, ce qui rend difficile la préparation des examens.', 'negatif', 3.0, 'Il y a 2 semaines'],
        ['Traitement du langage', 'Super cours ! Les projets sont stimulants et permettent d\'appliquer concrètement les concepts vus en cours.', 'positif', 4.7, 'Il y a 3 semaines'],
    ];
    @endphp

    <div id="listCommentaires">
        @foreach($commentaires as [$ue, $texte, $sentiment, $note, $date])
        <div class="card mb-3 commentaire-item" data-ue="{{ $ue }}" data-sentiment="{{ $sentiment }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge badge-sm badge-primary">{{ $ue }}</span>
                        @if($sentiment === 'positif')
                            <span class="badge badge-sm badge-success">Positif</span>
                        @elseif($sentiment === 'negatif')
                            <span class="badge badge-sm badge-danger">Critique</span>
                        @else
                            <span class="badge badge-sm badge-secondary">Neutre</span>
                        @endif
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <span style="color: {{ $i <= round($note) ? '#FFAB2D' : '#dee2e6' }}; font-size:14px;">★</span>
                            @endfor
                            <span class="ms-1 fs-12 text-muted">{{ $note }}/5</span>
                        </div>
                        <small class="text-muted">{{ $date }}</small>
                    </div>
                </div>
                <p class="mb-0 fs-14 text-dark fst-italic">"{{ $texte }}"</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-between align-items-center mt-2">
        <span class="text-muted fs-13">{{ count($commentaires) }} commentaires affichés</span>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function filterCommentaires() {
        const ue        = document.getElementById('filterUE').value.toLowerCase();
        const sentiment = document.getElementById('filterSentiment').value;
        const search    = document.getElementById('searchComment').value.toLowerCase();

        document.querySelectorAll('.commentaire-item').forEach(card => {
            const matchUE   = !ue   || card.dataset.ue.toLowerCase().includes(ue);
            const matchSent = !sentiment || card.dataset.sentiment === sentiment;
            const matchText = !search || card.textContent.toLowerCase().includes(search);
            card.style.display = (matchUE && matchSent && matchText) ? '' : 'none';
        });
    }

    ['filterUE', 'filterSentiment', 'searchComment'].forEach(id => {
        document.getElementById(id).addEventListener('input', filterCommentaires);
        document.getElementById(id).addEventListener('change', filterCommentaires);
    });
</script>
@endpush
