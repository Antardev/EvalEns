@extends('layouts.auth')

@section('title', 'Créer un compte')

@section('content')
<div class="col-xl-5 col-md-7 col-sm-10 mx-auto">
    <div class="authincation-content">
        <div class="row no-gutters">
            <div class="col-xl-12">
                <div class="auth-form">

                    {{-- Logo --}}
                    <div class="text-center mb-4">
                        <img src="{{ asset('dashboard/images/evalens-logo.svg') }}" alt="ÉvalENS" style="height:44px;">
                    </div>

                    <h4 class="text-center mb-1">Créer un compte</h4>
                    <p class="text-center text-muted fs-13 mb-4">Rejoignez la plateforme ÉvalENS</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @php
                        $roleParam = old('role', request('role'));
                        $roleLabels = [
                            'directeur'  => ['label' => 'Directeur',  'icon' => 'lni-briefcase'],
                            'enseignant' => ['label' => 'Enseignant', 'icon' => 'lni-blackboard'],
                        ];
                        $roleInfo = $roleLabels[$roleParam] ?? null;
                    @endphp

                    {{-- Profil sélectionné --}}
                    @if($roleInfo)
                    <div class="d-flex align-items-center justify-content-between mb-4 px-3 py-2 rounded"
                        style="background:#f0f3ff; border:1px solid #c7d0f8;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="lni {{ $roleInfo['icon'] }} text-primary" style="font-size:20px;"></i>
                            <span class="font-w600 text-primary">{{ $roleInfo['label'] }}</span>
                        </div>
                        <a href="{{ route('register') }}" class="fs-12 text-muted">Changer</a>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <input type="hidden" name="role" value="{{ $roleParam }}">

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="mb-1"><strong>Prénom</strong></label>
                                <input type="text" name="prenom"
                                    class="form-control @error('prenom') is-invalid @enderror"
                                    value="{{ old('prenom') }}"
                                    placeholder="Prénom"
                                    required autofocus>
                                @error('prenom')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="mb-1"><strong>Nom</strong></label>
                                <input type="text" name="nom"
                                    class="form-control @error('nom') is-invalid @enderror"
                                    value="{{ old('nom') }}"
                                    placeholder="Nom de famille"
                                    required>
                                @error('nom')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Sélection université + annexe (étudiant / enseignant uniquement) --}}
                        @if($roleParam === 'enseignant')

                        {{-- 1. Université --}}
                        <div class="form-group mb-3">
                            <label class="mb-1"><strong>Université <span class="text-danger">*</span></strong></label>
                            <select id="selectUniversite" name="university_id"
                                class="form-control @error('university_id') is-invalid @enderror"
                                required>
                                <option value="">— Sélectionnez votre université —</option>
                                @foreach($universities ?? [] as $univ)
                                    <option value="{{ $univ->id }}"
                                        {{ old('university_id') == $univ->id ? 'selected' : '' }}>
                                        {{ $univ->nom }}{{ $univ->acronyme ? ' ('.$univ->acronyme.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('university_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            @if(($universities ?? collect())->isEmpty())
                                <small class="text-warning mt-1 d-block">
                                    <i class="lni lni-warning me-1"></i>
                                    Aucune université disponible pour le moment.
                                </small>
                            @endif
                        </div>

                        {{-- 2. Annexe (filtrée selon l'université choisie) --}}
                        <div class="form-group mb-3" id="blocAnnexe" style="display:none;">
                            <label class="mb-1"><strong>Site / Annexe <span class="text-danger">*</span></strong></label>
                            <select id="selectAnnexe" name="annexe_id"
                                class="form-control @error('annexe_id') is-invalid @enderror">
                                <option value="">— Sélectionnez votre site —</option>
                            </select>
                            @error('annexe_id')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                            <small class="text-muted fs-12" id="msgAucuneAnnexe" style="display:none;">
                                <i class="lni lni-information me-1"></i>
                                Aucun site disponible pour cette université.
                            </small>
                        </div>

                        @endif

                        <div class="form-group mb-3">
                            <label class="mb-1"><strong>Adresse e-mail</strong></label>
                            <input type="email" name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                placeholder="votre@email.fr"
                                required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="mb-1"><strong>Mot de passe</strong></label>
                            <div class="position-relative">
                                <input type="password" name="password" id="regPassword"
                                    class="form-control pe-5 @error('password') is-invalid @enderror"
                                    placeholder="8 caractères minimum"
                                    required>
                                <button type="button" tabindex="-1"
                                    class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent text-muted"
                                    onclick="togglePassword('regPassword', this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label class="mb-1"><strong>Confirmer le mot de passe</strong></label>
                            <div class="position-relative">
                                <input type="password" name="password_confirmation" id="regPasswordConfirm"
                                    class="form-control pe-5"
                                    placeholder="Répéter le mot de passe"
                                    required>
                                <button type="button" tabindex="-1"
                                    class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent text-muted"
                                    onclick="togglePassword('regPasswordConfirm', this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Créer mon compte
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <p class="mb-0 fs-13">Déjà inscrit ?
                            <a class="text-primary" href="{{ route('login') }}">Se connecter</a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@php
    $annexesData = ($annexes ?? collect())->map(fn($a) => [
        'id'            => $a->id,
        'university_id' => $a->university_id,
        'label'         => $a->nom
                         . ($a->ville ? ' — ' . $a->ville : '')
                         . ($a->pays  ? ' (' . $a->pays . ')' : ''),
    ])->values();
@endphp

@push('scripts')
<script>
/* ── Sélection université → filtre annexes ── */
(function () {
    const allAnnexes = {!! json_encode($annexesData) !!};

    const selUniv   = document.getElementById('selectUniversite');
    const selAnnexe = document.getElementById('selectAnnexe');
    const bloc      = document.getElementById('blocAnnexe');
    const msgVide   = document.getElementById('msgAucuneAnnexe');

    if (!selUniv) return;

    function filtrerAnnexes() {
        const univId = parseInt(selUniv.value);
        const filtered = allAnnexes.filter(a => a.university_id === univId);

        // Vider + repeupler le select
        selAnnexe.innerHTML = '<option value="">— Sélectionnez votre site —</option>';
        filtered.forEach(function (a) {
            const opt = document.createElement('option');
            opt.value = a.id;
            opt.textContent = a.label;
            if (a.id == {{ old('annexe_id', 0) }}) opt.selected = true;
            selAnnexe.appendChild(opt);
        });

        bloc.style.display    = univId ? '' : 'none';
        msgVide.style.display = (univId && filtered.length === 0) ? '' : 'none';
        selAnnexe.required    = univId && filtered.length > 0;
    }

    selUniv.addEventListener('change', filtrerAnnexes);

    // Restauration après retour de validation (old())
    if (selUniv.value) { filtrerAnnexes(); }
})();
</script>
<script>
const SVG_EYE = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/></svg>`;
const SVG_EYE_SLASH = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/></svg>`;

function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
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

@endsection
