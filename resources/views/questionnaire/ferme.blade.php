@extends('layouts.questionnaire')

@php
    $isExpire = $lien->expire_at && $lien->expire_at->isPast();
@endphp

@section('title', $isExpire ? 'Questionnaire expiré' : 'Questionnaire fermé')

@section('content')
<div class="row justify-content-center">
<div class="col-xl-5 col-lg-6 col-md-8 col-12">
    <div class="card border-0 shadow-sm text-center py-5 px-4">
        <div class="mb-4">

            @if($isExpire)
                <div class="d-flex align-items-center justify-content-center rounded-circle mx-auto mb-3"
                    style="width:80px;height:80px;background:#fdecea;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#c62828" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path d="M7.5 4.5a.5.5 0 0 1 1 0v4a.5.5 0 0 1-1 0v-4zm.5 6.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5z"/>
                    </svg>
                </div>
                <h3 class="font-w700 mb-2" style="color:#c62828;">Questionnaire expiré</h3>
                <p class="text-muted">
                    Ce questionnaire n'est plus accessible.<br>
                    Il a expiré le <strong>{{ $lien->expire_at->format('d/m/Y à H:i') }}</strong>.
                </p>
            @else
                <div class="d-flex align-items-center justify-content-center rounded-circle mx-auto mb-3"
                    style="width:80px;height:80px;background:#fff3e0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#e65100" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                    </svg>
                </div>
                <h3 class="font-w700 mb-2" style="color:#e65100;">Questionnaire fermé</h3>
                <p class="text-muted">
                    Ce questionnaire n'accepte plus de réponses.
                </p>
            @endif

        </div>
        <p class="text-muted fs-13 mb-0">
            Contactez votre gestionnaire si vous pensez qu'il s'agit d'une erreur.
        </p>
    </div>
</div>
</div>
@endsection
