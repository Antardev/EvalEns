@extends('layouts.superadmin')

@section('title', 'Gestion des utilisateurs')

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Utilisateurs</h2>
            <p class="mb-0">Liste et gestion de tous les comptes utilisateurs</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filtres --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('superadmin.utilisateurs') }}" class="d-flex flex-wrap gap-2">
                <input type="text" name="search" class="form-control w-auto"
                    placeholder="Nom ou email..."
                    value="{{ request('search') }}">
                <select name="university_id" class="form-select w-auto">
                    <option value="">Toutes les universités</option>
                    @foreach($universities as $univ)
                        <option value="{{ $univ->id }}" {{ request('university_id') == $univ->id ? 'selected' : '' }}>
                            {{ $univ->nom }}
                        </option>
                    @endforeach
                </select>
                <select name="role" class="form-select w-auto">
                    <option value="">Tous les rôles</option>
                    <option value="etudiant"   {{ request('role') === 'etudiant'   ? 'selected' : '' }}>Étudiant</option>
                    <option value="enseignant" {{ request('role') === 'enseignant' ? 'selected' : '' }}>Enseignant</option>
                    <option value="directeur"  {{ request('role') === 'directeur'  ? 'selected' : '' }}>Directeur</option>
                </select>
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="lni lni-search-alt me-1"></i>Filtrer
                </button>
                @if(request()->hasAny(['search', 'university_id', 'role']))
                    <a href="{{ route('superadmin.utilisateurs') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="lni lni-close me-1"></i>Réinitialiser
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted fs-13">{{ $counts['total'] }} utilisateur(s) au total</span>
                <div class="d-flex gap-2">
                    <span class="badge badge-info badge-sm">Étudiants : {{ $counts['etudiants'] }}</span>
                    <span class="badge badge-warning badge-sm">Enseignants : {{ $counts['enseignants'] }}</span>
                    <span class="badge badge-success badge-sm">Directeurs : {{ $counts['directeurs'] }}</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nom complet</th>
                            <th>Email</th>
                            <th>Université</th>
                            <th>Rôle</th>
                            <th>Inscrit le</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td><span class="font-w500">{{ $user->prenom }} {{ $user->nom }}</span></td>
                            <td class="text-muted fs-13">{{ $user->email }}</td>
                            <td>{{ $user->university?->nom ?? '—' }}</td>
                            <td>
                                @php
                                    $roleColors = [
                                        'etudiant'   => 'info',
                                        'enseignant' => 'warning',
                                        'directeur'  => 'success',
                                    ];
                                    $roleLabels = [
                                        'etudiant'   => 'Étudiant',
                                        'enseignant' => 'Enseignant',
                                        'directeur'  => 'Directeur',
                                    ];
                                @endphp
                                <span class="badge badge-xs badge-{{ $roleColors[$user->role] ?? 'secondary' }}">
                                    {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="text-muted fs-13">{{ $user->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="lni lni-users fs-24 d-block mb-2"></i>
                                Aucun utilisateur trouvé.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="text-muted fs-13">
                    Affichage {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }}
                    sur {{ $users->total() }} entrées
                </span>
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

</div>
@endsection
