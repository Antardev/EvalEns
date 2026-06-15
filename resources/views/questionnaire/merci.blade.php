@extends('layouts.questionnaire')

@section('title', 'Merci pour votre évaluation')

@section('content')
<div class="row justify-content-center">
<div class="col-xl-5 col-lg-6 col-md-8 col-12">
    <div class="card border-0 shadow-sm text-center py-5 px-4">
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-center rounded-circle mx-auto mb-3"
                style="width:80px;height:80px;background:#e8f5e9;">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#2e7d32" viewBox="0 0 16 16">
                    <path d="M13.485 1.431a1.473 1.473 0 0 1 2.104 2.062l-7.84 9.801a1.473 1.473 0 0 1-2.12.04L.431 8.138a1.473 1.473 0 0 1 2.084-2.083l4.111 4.112 6.82-8.69a.486.486 0 0 1 .04-.046z"/>
                </svg>
            </div>
            <h3 class="font-w700 text-success mb-2">Merci pour votre évaluation !</h3>
            <p class="text-muted">Votre réponse a bien été enregistrée pour :</p>
        </div>

        <div class="bg-light rounded p-3 mb-4 text-start">
            <p class="mb-1"><strong>Questionnaire :</strong> {{ $lien->titre }}</p>
            <p class="mb-1"><strong>Classe :</strong> {{ $lien->classe }}</p>
            @if($lien->matiere)
                <p class="mb-1"><strong>Matière :</strong> {{ $lien->matiere }}</p>
            @endif
            @if($lien->enseignant)
                <p class="mb-0"><strong>Enseignant :</strong> {{ $lien->enseignant->prenom }} {{ $lien->enseignant->nom }}</p>
            @endif
        </div>

        <p class="text-muted fs-13">
            Vos réponses sont anonymes et contribuent à l'amélioration de la qualité des enseignements.
        </p>

        <p class="text-muted fs-12 mt-2 mb-0">Vous pouvez fermer cette page.</p>
    </div>
</div>
</div>
@endsection
