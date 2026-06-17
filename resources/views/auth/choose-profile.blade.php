@extends('layouts.auth')

@section('title', 'Choisissez votre profil')

@push('styles')
<style>
    .role-card {
        border: 2px solid #dee2e6;
        border-radius: 12px;
        transition: border-color .2s, transform .2s, box-shadow .2s;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }
    .role-card:hover {
        border-color: #2F4CDD;
        transform: translateY(-5px);
        box-shadow: 0 10px 28px rgba(47, 76, 221, .15);
        color: inherit;
    }
    .role-card:hover .role-icon { color: #2F4CDD; }
    .role-icon {
        font-size: 48px;
        color: #b1b1b1;
        transition: color .2s;
    }
</style>
@endpush

@section('content')
<div class="col-xl-8 col-lg-9 col-md-11 mx-auto">

    <div class="text-center mb-5">
        <img src="{{ asset('dashboard/evalens-logo.png') }}" alt="ÉvalENS" style="height:120px; width:50%; ">
        <h3 class="mt-1 mb-1 font-w600">Choisissez votre profil</h3>
        <p class="text-muted fs-14">pour commencer votre inscription</p>
    </div>

    <div class="row g-4 justify-content-center">

        <div class="col-md-4 col-sm-6">
            <a href="{{ route('register.form') }}?role=directeur" class="role-card d-block text-center p-4">
                <i class="lni lni-briefcase role-icon mb-3 d-block"></i>
                <h5 class="font-w600 mb-2">Directeur</h5>
                <p class="text-muted fs-13 mb-0">
                    Gérez votre université, les enseignants et les périodes d'évaluation
                </p>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="{{ route('register.form') }}?role=enseignant" class="role-card d-block text-center p-4">
                <i class="lni lni-blackboard role-icon mb-3 d-block"></i>
                <h5 class="font-w600 mb-2">Enseignant</h5>
                <p class="text-muted fs-13 mb-0">
                    Consultez vos évaluations et suivez votre progression pédagogique
                </p>
            </a>
        </div>

    </div>

    <div class="text-center mt-5">
        <p class="fs-13 text-muted mb-0">
            Déjà inscrit ? <a href="{{ route('login') }}" class="text-primary">Se connecter</a>
        </p>
    </div>

</div>
@endsection
