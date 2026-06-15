@extends('layouts.app')

@section('title', 'Enseignants — ' . $annexe->nom)

@section('content')
<div class="container-fluid">

    <div class="form-head d-flex mb-3 align-items-start">
        <div class="me-auto d-none d-lg-block">
            <h2 class="text-primary font-w600 mb-0">Enseignants</h2>
            <p class="mb-0">{{ $annexe->nom }} — {{ $annexe->ville ?? '' }}</p>
        </div>
        <span class="badge badge-success px-3 py-2 fs-13">{{ $total }} enseignant{{ $total !== 1 ? 's' : '' }}</span>
    </div>

    {{-- Recherche --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('gestionnaire.enseignants') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <input type="text" name="search" class="form-control w-auto"
                    placeholder="Rechercher un enseignant..."
                    value="{{ request('search') }}"
                    style="min-width:220px;">
                <button type="submit" class="btn btn-primary">
                    <i class="lni lni-search-alt me-1"></i>Rechercher
                </button>
                @if(request('search'))
                    <a href="{{ route('gestionnaire.enseignants') }}" class="btn btn-outline-secondary">
                        <i class="lni lni-close me-1"></i>Effacer
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($membres->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="lni lni-blackboard fs-1 d-block mb-3"></i>
                    Aucun enseignant trouvé{{ request('search') ? ' pour cette recherche' : ' dans cette annexe' }}.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Inscrit le</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($membres as $m)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold"
                                            style="width:36px;height:36px;flex-shrink:0;font-size:13px;background:#2BC155;">
                                            {{ strtoupper(substr($m->prenom, 0, 1)) }}
                                        </div>
                                        <div class="font-w500">{{ $m->prenom }} {{ $m->nom }}</div>
                                    </div>
                                </td>
                                <td class="text-muted fs-13">{{ $m->email }}</td>
                                <td class="text-muted fs-13">{{ $m->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span class="text-muted fs-13">
                        {{ $membres->firstItem() }}–{{ $membres->lastItem() }} sur {{ $membres->total() }} enseignants
                    </span>
                    {{ $membres->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
