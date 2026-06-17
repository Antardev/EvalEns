@extends('layouts.auth')

@section('title', 'Créer un compte')

@section('content')
<div class="col-xl-5 col-md-7 col-sm-10 mx-auto">
    <div class="authincation-content">
        <div class="row no-gutters">
            <div class="col-xl-12">
                <div class="auth-form">

                    {{-- Logo --}}
                    <div class="text-center mb-1">
                        <img src="{{ asset('dashboard/evalens-logo.png') }}" alt="ÉvalENS" style="width:100%; max-height:120px; object-fit:contain;">
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

                        {{-- Sélection université + annexes (enseignant uniquement) --}}
                        @if($roleParam === 'enseignant')
                        @php
                            $oldAnnexeIds   = array_map('intval', old('annexe_ids', []));
                            $allUnivJson    = ($universities ?? collect())->map(fn($u) => [
                                'id'      => $u->id,
                                'nom'     => $u->nom,
                                'acronyme'=> $u->acronyme ?? '',
                            ])->values();
                            $allAnnexesJson = ($annexes ?? collect())->map(fn($a) => [
                                'id'            => $a->id,
                                'university_id' => $a->university_id,
                                'nom'           => $a->nom,
                                'ville'         => $a->ville ?? '',
                            ])->values();
                        @endphp

                        @error('annexe_ids')
                            <div class="alert alert-danger py-2 fs-13 mb-2">{{ $message }}</div>
                        @enderror

                        {{-- Étape 1 : Universités --}}
                        <div class="mb-3">
                            <label class="mb-2 d-block"><strong>Université(s) <span class="text-danger">*</span></strong></label>
                            <div id="univCards" class="d-flex flex-wrap gap-2"></div>
                        </div>

                        {{-- Étape 2 : Annexes (apparaît après sélection université) --}}
                        <div id="blocAnnexes" class="mb-3" style="display:none;">
                            <label class="mb-2 d-block"><strong>Annexe(s) <span class="text-danger">*</span></strong></label>
                            <div id="annexeChips" class="d-flex flex-wrap gap-2"></div>
                            <small id="msgAucuneAnnexe" class="text-warning mt-1" style="display:none;">
                                <i class="lni lni-warning me-1"></i>Aucune annexe disponible pour cette université.
                            </small>
                        </div>

                        {{-- Champs hidden soumis au formulaire --}}
                        <div id="hiddenAnnexes"></div>

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
@push('scripts')
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

/* ── Sélection université → annexes (cartes cliquables) ── */
(function () {
    const univCardsEl  = document.getElementById('univCards');
    const annexeChipsEl= document.getElementById('annexeChips');
    const blocAnnexes  = document.getElementById('blocAnnexes');
    const msgVide      = document.getElementById('msgAucuneAnnexe');
    const hiddenDiv    = document.getElementById('hiddenAnnexes');
    if (!univCardsEl) return;

    const allUnivs   = {!! json_encode($allUnivJson ?? []) !!};
    const allAnnexes = {!! json_encode($allAnnexesJson ?? []) !!};
    const oldIds     = {!! json_encode(array_map('intval', old('annexe_ids', []))) !!};

    let selectedUnivIds  = [];
    let selectedAnnexIds = [...oldIds];

    const CARD_BASE = 'univ-card d-flex align-items-center gap-2 px-3 py-2 rounded border cursor-pointer';
    const CARD_OFF  = 'bg-white border-secondary text-secondary';
    const CARD_ON   = 'bg-primary border-primary text-white';

    const CHIP_BASE = 'annexe-chip d-flex align-items-center gap-1 px-3 py-2 rounded-pill border cursor-pointer fs-13';
    const CHIP_OFF  = 'bg-white border-secondary text-secondary';
    const CHIP_ON   = 'bg-success border-success text-white';

    /* ─ Build university cards ─ */
    allUnivs.forEach(function (u) {
        const card = document.createElement('div');
        card.className = CARD_BASE + ' ' + CARD_OFF;
        card.dataset.id = u.id;
        card.style.cssText = 'cursor:pointer;transition:.15s;user-select:none;font-size:13px;';
        card.innerHTML = '<i class="lni lni-university" style="font-size:16px;"></i>'
            + '<span class="fw-semibold">' + u.nom + (u.acronyme ? ' <small class="opacity-75">(' + u.acronyme + ')</small>' : '') + '</span>'
            + '<i class="lni lni-checkmark-circle ms-1 check-icon" style="display:none;font-size:14px;"></i>';

        card.addEventListener('click', function () {
            const id = parseInt(this.dataset.id);
            if (selectedUnivIds.includes(id)) {
                selectedUnivIds = selectedUnivIds.filter(x => x !== id);
                this.className = CARD_BASE + ' ' + CARD_OFF;
                this.querySelector('.check-icon').style.display = 'none';
                // deselect annexes of this univ
                allAnnexes.filter(a => a.university_id === id).forEach(a => {
                    selectedAnnexIds = selectedAnnexIds.filter(x => x !== a.id);
                });
            } else {
                selectedUnivIds.push(id);
                this.className = CARD_BASE + ' ' + CARD_ON;
                this.querySelector('.check-icon').style.display = '';
            }
            renderAnnexes();
            updateHidden();
        });

        univCardsEl.appendChild(card);
    });

    /* ─ Render annexe chips for selected univs ─ */
    function renderAnnexes() {
        annexeChipsEl.innerHTML = '';
        const filtered = allAnnexes.filter(a => selectedUnivIds.includes(a.university_id));
        blocAnnexes.style.display  = selectedUnivIds.length ? '' : 'none';
        msgVide.style.display      = (selectedUnivIds.length && filtered.length === 0) ? '' : 'none';

        filtered.forEach(function (a) {
            const chip = document.createElement('div');
            const isOn = selectedAnnexIds.includes(a.id);
            chip.className = CHIP_BASE + ' ' + (isOn ? CHIP_ON : CHIP_OFF);
            chip.dataset.id = a.id;
            chip.style.cssText = 'cursor:pointer;transition:.15s;user-select:none;';
            chip.innerHTML = '<i class="lni lni-map-marker" style="font-size:13px;"></i>'
                + '<span>' + a.nom + (a.ville ? ' <span class="opacity-75">— ' + a.ville + '</span>' : '') + '</span>'
                + (isOn ? '<i class="lni lni-close" style="font-size:11px;"></i>' : '');

            chip.addEventListener('click', function () {
                const id = parseInt(this.dataset.id);
                if (selectedAnnexIds.includes(id)) {
                    selectedAnnexIds = selectedAnnexIds.filter(x => x !== id);
                    this.className = CHIP_BASE + ' ' + CHIP_OFF;
                    this.querySelector('.lni-close') && this.querySelector('.lni-close').remove();
                } else {
                    selectedAnnexIds.push(id);
                    this.className = CHIP_BASE + ' ' + CHIP_ON;
                    if (!this.querySelector('.lni-close')) {
                        const x = document.createElement('i');
                        x.className = 'lni lni-close';
                        x.style.fontSize = '11px';
                        this.appendChild(x);
                    }
                }
                updateHidden();
            });

            annexeChipsEl.appendChild(chip);
        });
    }

    /* ─ Sync hidden inputs ─ */
    function updateHidden() {
        hiddenDiv.innerHTML = '';
        selectedAnnexIds.forEach(function (id) {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'annexe_ids[]';
            inp.value = id;
            hiddenDiv.appendChild(inp);
        });
    }

    /* ─ Restore after validation error ─ */
    if (oldIds.length) {
        const univIds = [...new Set(allAnnexes.filter(a => oldIds.includes(a.id)).map(a => a.university_id))];
        univIds.forEach(function (uid) {
            const card = univCardsEl.querySelector('[data-id="' + uid + '"]');
            if (card) {
                selectedUnivIds.push(uid);
                card.className = CARD_BASE + ' ' + CARD_ON;
                card.querySelector('.check-icon').style.display = '';
            }
        });
        renderAnnexes();
        updateHidden();
    }
})();
</script>
@endpush

@endsection
