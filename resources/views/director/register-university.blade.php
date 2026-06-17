@extends('layouts.auth')

@section('title', 'Inscrire mon université')

@section('content')
<div class="col-xl-6 col-md-8 col-sm-11 mx-auto">
    <div class="authincation-content">
        <div class="row no-gutters">
            <div class="col-xl-12">
                <div class="auth-form">

                    <div class="text-center mb-4">
                        <img src="{{ asset('dashboard/evalens-logo.png') }}" alt="ÉvalENS" style="height:44px;">
                    </div>

                    <h4 class="text-center mb-1">Inscrire mon université</h4>
                    <p class="text-center text-muted fs-13 mb-4">
                        Bienvenue <strong>{{ auth()->user()->prenom }}</strong> ! Renseignez les informations de votre établissement.
                        Votre accès sera activé après validation par l'administrateur ÉvalENS.
                    </p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('director.store-university') }}">
                        @csrf

                        {{-- Université + Acronyme --}}
                        <div class="row g-3 mb-3">
                            <div class="col-8">
                                <label class="mb-1"><strong>Université <span class="text-danger">*</span></strong></label>
                                <select id="select-universite" name="nom"
                                    class="form-control @error('nom') is-invalid @enderror"
                                    required>
                                    <option value="" disabled {{ old('nom') ? '' : 'selected' }}>
                                        — Sélectionnez —
                                    </option>
                                    @foreach($universities as $univ)
                                        <option value="{{ $univ->nom }}"
                                                data-acronyme="{{ $univ->acronyme ?? '' }}"
                                                {{ old('nom') === $univ->nom ? 'selected' : '' }}>
                                            {{ $univ->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('nom')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-4">
                                <label class="mb-1"><strong>Acronyme</strong></label>
                                <input type="text" id="input-acronyme" name="acronyme"
                                    class="form-control @error('acronyme') is-invalid @enderror"
                                    value="{{ old('acronyme') }}"
                                    placeholder="—"
                                    maxlength="20"
                                    readonly
                                    style="background:#f4f7ff; cursor:default;">
                                @error('acronyme')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>



                        {{-- Email + Téléphone --}}
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="mb-1"><strong>Email institutionnel <span class="text-danger">*</span></strong></label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}"
                                    placeholder="contact@universite.com" required>
                                @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-6">
                                <label class="mb-1"><strong>Téléphone</strong></label>
                                <input type="text" name="telephone"
                                    class="form-control @error('telephone') is-invalid @enderror"
                                    value="{{ old('telephone') }}"
                                    placeholder="">
                                @error('telephone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Site web --}}
                        <div class="form-group mb-4">
                            <label class="mb-1"><strong>Site web</strong></label>
                            <input type="url" name="site_web"
                                class="form-control @error('site_web') is-invalid @enderror"
                                value="{{ old('site_web') }}"
                                placeholder="https://www.universite.bj">
                            @error('site_web') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="alert alert-info fs-13 py-2 mb-3">
                            <i class="lni lni-information me-1"></i>
                            Les sites/annexes (adresse, ville, pays) seront configurés après validation de votre accès, depuis votre tableau de bord.
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="lni lni-checkmark me-1"></i>Soumettre la demande d'inscription
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link fs-13 text-muted p-0">Se déconnecter</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const sel   = document.getElementById('select-universite');
    const acro  = document.getElementById('input-acronyme');

    if (!sel || !acro) return;

    function syncAcronyme() {
        const opt = sel.options[sel.selectedIndex];
        acro.value = (opt && opt.dataset.acronyme) ? opt.dataset.acronyme : '';
    }

    sel.addEventListener('change', syncAcronyme);

    /* Restauration après retour de validation (old()) */
    if (sel.value) { syncAcronyme(); }
})();
</script>
@endpush
