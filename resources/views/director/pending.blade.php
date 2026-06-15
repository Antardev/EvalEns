@extends('layouts.auth')

@section('title', 'Demande en cours de traitement')

@push('styles')
<style>
    .pending-icon { font-size: 64px; }
    .status-card  { border-radius: 16px; }
</style>
@endpush

@section('content')
<div class="col-xl-5 col-md-7 col-sm-10 mx-auto">
    <div class="text-center mb-4">
        <img src="{{ asset('dashboard/images/evalens-logo.svg') }}" alt="ÉvalENS" style="height:44px;">
    </div>

    @if($university->isEnAttente())
    {{-- ---- EN ATTENTE ---- --}}
    <div class="card status-card text-center px-4 py-5">
        <div class="pending-icon mb-3">⏳</div>
        <h4 class="font-w600 mb-2">Demande en cours de validation</h4>
        <p class="text-muted fs-13 mb-4">
            Votre demande d'inscription pour <strong>{{ $university->nom }}</strong>
            a bien été soumise. L'équipe ÉvalENS l'examine et vous donnera accès
            dès validation.
        </p>

        <div class="alert alert-info fs-13 text-start mb-4">
            <i class="lni lni-information me-2"></i>
            Vous recevrez une notification par e-mail à l'adresse
            <strong>{{ auth()->user()->email }}</strong> lorsque votre demande sera traitée.
        </div>

        <div class="card bg-light border-0 text-start p-3 mb-4">
            <p class="fs-13 mb-1"><strong>Établissement :</strong> {{ $university->nom }}
                @if($university->acronyme) ({{ $university->acronyme }}) @endif
            </p>
            <p class="fs-13 mb-1"><strong>Ville :</strong> {{ $university->ville }}, {{ $university->pays }}</p>
            <p class="fs-13 mb-1"><strong>Email :</strong> {{ $university->email }}</p>
            <p class="fs-13 mb-0"><strong>Soumis le :</strong> {{ $university->created_at->format('d/m/Y à H:i') }}</p>
        </div>

        <span class="badge badge-warning px-4 py-2 fs-13">En attente de validation</span>
    </div>

    @elseif($university->isRejetee())
    {{-- ---- REJETÉE ---- --}}
    <div class="card status-card text-center px-4 py-5">
        <div class="pending-icon mb-3">❌</div>
        <h4 class="font-w600 mb-2 text-danger">Demande refusée</h4>
        <p class="text-muted fs-13 mb-4">
            Votre demande d'inscription pour <strong>{{ $university->nom }}</strong>
            n'a pas été acceptée.
        </p>

        @if($university->motif_rejet)
        <div class="alert alert-danger fs-13 text-start mb-4">
            <strong>Motif du refus :</strong><br>
            {{ $university->motif_rejet }}
        </div>
        @endif

        <p class="text-muted fs-13 mb-4">
            Vous pouvez soumettre une nouvelle demande avec des informations corrigées.
        </p>

        <a href="{{ route('director.register-university') }}"
            class="btn btn-primary w-100 mb-2"
            onclick="
                @this.university_id = null;
            ">
            <i class="lni lni-reload me-1"></i>Soumettre une nouvelle demande
        </a>
    </div>
    @endif

    <div class="text-center mt-3">
        <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-link fs-13 text-muted p-0">Se déconnecter</button>
        </form>
    </div>

</div>
@endsection
