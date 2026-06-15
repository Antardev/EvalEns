@extends('layouts.app')

@section('title', 'Mon profil')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-4 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Mon profil</h2>
            <p class="mb-0">Gérer vos informations personnelles et votre mot de passe</p>
        </div>
    </div>

    <div class="row g-4">

        {{-- Carte avatar + résumé --}}
        <div class="col-xl-3 col-lg-4 col-12">
            <div class="card text-center">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold mx-auto mb-3"
                        style="width:80px;height:80px;font-size:28px;background:#2F4CDD;">
                        {{ strtoupper(substr($user->prenom, 0, 1)) }}
                    </div>
                    <h5 class="font-w600 mb-1">{{ $user->prenom }} {{ $user->nom }}</h5>
                    <p class="text-muted fs-13 mb-2">{{ $user->email }}</p>
                    @php
                        $roleLabels = [
                            'superadmin'   => ['label' => 'Super Administrateur', 'class' => 'badge-danger'],
                            'directeur'    => ['label' => 'Directeur',            'class' => 'badge-primary'],
                            'gestionnaire' => ['label' => 'Gestionnaire',         'class' => 'badge-info'],
                            'enseignant'   => ['label' => 'Enseignant',           'class' => 'badge-success'],
                        ];
                        $rl = $roleLabels[$user->role] ?? ['label' => $user->role, 'class' => 'badge-secondary'];
                    @endphp
                    <span class="badge {{ $rl['class'] }} px-3 py-2">{{ $rl['label'] }}</span>

                    @if($user->annexe)
                        <div class="mt-3 pt-3 border-top text-start">
                            <p class="text-muted fs-12 mb-1">Annexe</p>
                            <p class="font-w500 fs-13 mb-0">{{ $user->annexe->nom }}</p>
                            @if($user->annexe->ville)
                                <p class="text-muted fs-12 mb-0">{{ $user->annexe->ville }}</p>
                            @endif
                        </div>
                    @endif

                    @if($user->university)
                        <div class="mt-2 pt-2 border-top text-start">
                            <p class="text-muted fs-12 mb-1">Université</p>
                            <p class="font-w500 fs-13 mb-0">{{ $user->university->nom }}</p>
                        </div>
                    @endif

                    <div class="mt-3 pt-3 border-top text-start">
                        <p class="text-muted fs-12 mb-1">Membre depuis</p>
                        <p class="font-w500 fs-13 mb-0">{{ $user->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Formulaires --}}
        <div class="col-xl-9 col-lg-8 col-12">

            {{-- Informations personnelles --}}
            <div class="card mb-4">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-0">
                        <i class="lni lni-user me-2 text-primary"></i>Informations personnelles
                    </h4>
                </div>
                <div class="card-body">

                    @if(session('success_info'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="lni lni-checkmark-circle me-2"></i>{{ session('success_info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->has('prenom') || $errors->has('nom') || $errors->has('email'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                @foreach(['prenom','nom','email'] as $field)
                                    @error($field)<li>{{ $message }}</li>@enderror
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update-info') }}">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                                <input type="text" name="prenom"
                                    class="form-control @error('prenom') is-invalid @enderror"
                                    value="{{ old('prenom', $user->prenom) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                                <input type="text" name="nom"
                                    class="form-control @error('nom') is-invalid @enderror"
                                    value="{{ old('nom', $user->nom) }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Adresse e-mail <span class="text-danger">*</span></label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="lni lni-save me-1"></i>Enregistrer les modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Mot de passe --}}
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-0">
                        <i class="lni lni-lock me-2 text-primary"></i>Changer le mot de passe
                    </h4>
                </div>
                <div class="card-body">

                    @if(session('success_password'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="lni lni-checkmark-circle me-2"></i>{{ session('success_password') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->has('current_password') || $errors->has('password'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                @error('current_password')<li>{{ $message }}</li>@enderror
                                @error('password')<li>{{ $message }}</li>@enderror
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update-password') }}">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Mot de passe actuel <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <input type="password" name="current_password" id="pwdCurrent"
                                        class="form-control pe-5 @error('current_password') is-invalid @enderror"
                                        placeholder="Votre mot de passe actuel" required>
                                    <button type="button" tabindex="-1"
                                        class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent text-muted"
                                        onclick="togglePwd('pwdCurrent', this)">
                                        {!! svgEye() !!}
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nouveau mot de passe <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <input type="password" name="password" id="pwdNew"
                                        class="form-control pe-5 @error('password') is-invalid @enderror"
                                        placeholder="8 caractères minimum" required>
                                    <button type="button" tabindex="-1"
                                        class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent text-muted"
                                        onclick="togglePwd('pwdNew', this)">
                                        {!! svgEye() !!}
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirmer le nouveau mot de passe <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <input type="password" name="password_confirmation" id="pwdConfirm"
                                        class="form-control pe-5"
                                        placeholder="Répéter le mot de passe" required>
                                    <button type="button" tabindex="-1"
                                        class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent text-muted"
                                        onclick="togglePwd('pwdConfirm', this)">
                                        {!! svgEye() !!}
                                    </button>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="lni lni-lock me-1"></i>Mettre à jour le mot de passe
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@php
function svgEye() {
    return '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/></svg>';
}
@endphp

@push('scripts')
<script>
const SVG_EYE      = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/></svg>`;
const SVG_EYE_SLASH = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/></svg>`;

function togglePwd(id, btn) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = SVG_EYE_SLASH;
    } else {
        input.type = 'password';
        btn.innerHTML = SVG_EYE;
    }
}
</script>
@endpush
