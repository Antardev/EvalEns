@extends('layouts.questionnaire')

@section('title', 'Questionnaire pas encore disponible')

@section('content')
<div class="container-fluid d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="text-center">
        <i class="lni lni-hourglass fs-1 text-warning d-block mb-3"></i>
        <h4 class="font-w600 mb-2">Questionnaire pas encore disponible</h4>
        <p class="text-muted mb-1">« {{ $lien->titre }} »</p>
        <p class="text-muted">
            Ce questionnaire ouvrira le <strong>{{ $lien->debut_at->format('d/m/Y à H:i') }}</strong>.
        </p>
    </div>
</div>
@endsection
