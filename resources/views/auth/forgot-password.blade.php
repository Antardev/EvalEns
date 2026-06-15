@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
<div class="col-md-6">
    <div class="authincation-content">
        <div class="row no-gutters">
            <div class="col-xl-12">
                <div class="auth-form">
                    <h4 class="text-center mb-4">Forgot Password</h4>
                    <p class="text-center mb-4">Enter your email address and we'll send you a link to reset your password.</p>

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="form-group">
                            <label class="mb-1"><strong>Email</strong></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="hello@example.com" required autofocus>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
                        </div>
                    </form>
                    <div class="new-account mt-3">
                        <p>Remember your password? <a class="text-primary" href="{{ route('login') }}">Sign in</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
