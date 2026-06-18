@extends('layouts.auth')

@section('title', 'Mot de passe oublié')

@section('content')
<div class="col-xl-4 col-md-6 col-sm-10 mx-auto">
    <div class="authincation-content">
        <div class="row no-gutters">
            <div class="col-xl-12">
                <div class="auth-form">

                    {{-- Logo --}}
                    <div class="d-flex justify-content-center align-items-center mb-3">
                        <img src="{{ asset('dashboard/evalens-logo.png') }}" alt="ÉvalENS" style="height:90px; max-width:100%;">
                    </div>

                    <h4 class="text-center mb-1">Mot de passe oublié ?</h4>
                    <p class="text-center text-muted fs-13 mb-4">
                        Saisissez votre adresse e-mail et nous vous enverrons un lien pour réinitialiser votre mot de passe.
                    </p>

                    @if(session('status'))
                        <div class="alert alert-success d-flex align-items-center gap-2 fs-13">
                            <i class="lni lni-checkmark-circle fs-16"></i>
                            {{ session('status') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger fs-13">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="form-group mb-4">
                            <label class="mb-1"><strong>Adresse e-mail</strong></label>
                            <input type="email" name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                placeholder="votre@email.fr"
                                required autofocus>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="lni lni-envelope me-1"></i>Envoyer le lien de réinitialisation
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <p class="mb-0 fs-13">
                            <a class="text-primary" href="{{ route('login') }}">
                                <i class="lni lni-arrow-left me-1"></i>Retour à la connexion
                            </a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
