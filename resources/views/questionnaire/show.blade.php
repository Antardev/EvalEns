@extends('layouts.questionnaire')

@section('title', $lien->titre)

@section('content')
<div class="row justify-content-center">
<div class="col-xl-7 col-lg-8 col-md-10 col-12">

    {{-- En-tête questionnaire --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-4">
            <h3 class="font-w700 mb-1 text-primary">{{ $lien->titre }}</h3>
            <div class="d-flex flex-wrap gap-3 mt-2">
                <span class="badge badge-primary px-3 py-2">
                    <i class="lni lni-graduation me-1"></i>{{ $lien->classe }}
                </span>
                @if($lien->matiere)
                    <span class="badge badge-info px-3 py-2">{{ $lien->matiere }}</span>
                @endif
                @if($lien->enseignant)
                    <span class="badge badge-success px-3 py-2">
                        <i class="lni lni-user me-1"></i>{{ $lien->enseignant->prenom }} {{ $lien->enseignant->nom }}
                    </span>
                @endif
            </div>
            <p class="text-muted fs-13 mt-3 mb-0">
                Évaluez chaque critère de 1 (très insuffisant) à 5 (excellent). Vos réponses sont anonymes.
            </p>
        </div>
    </div>

    {{-- Erreurs --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="lni lni-warning me-2"></i>Veuillez noter tous les critères avant de soumettre.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('questionnaire.submit', $lien->token) }}">
        @csrf

        {{-- Critères --}}
        @foreach($lien->questions as $i => $question)
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <h6 class="font-w600 mb-0">{{ $i + 1 }}. {{ $question['label'] }}</h6>
                    <span class="badge badge-light text-dark fs-12" id="val-{{ $i }}">—</span>
                </div>
                @if(!empty($question['description']))
                    <p class="text-muted fs-12 mb-3">{{ $question['description'] }}</p>
                @endif

                <div class="star-group mb-1" role="group" aria-label="{{ $question['label'] }}">
                    @foreach([1 => 'Très insuffisant', 2 => 'Insuffisant', 3 => 'Correct', 4 => 'Bien', 5 => 'Excellent'] as $val => $label)
                        <div>
                            <input type="radio" name="scores[{{ $i }}]" id="s{{ $i }}_{{ $val }}"
                                value="{{ $val }}" {{ old("scores.$i") == $val ? 'checked' : '' }}
                                onchange="updateLabel({{ $i }}, {{ $val }}, '{{ $label }}')">
                            <label class="star-label" for="s{{ $i }}_{{ $val }}" title="{{ $label }}">{{ $val }}</label>
                        </div>
                    @endforeach
                </div>
                <div class="score-legend mt-1">
                    <span>1 — Très insuffisant</span>
                    <span>5 — Excellent</span>
                </div>
                @error("scores.$i")
                    <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>
        @endforeach

        {{-- Commentaire libre --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body p-4">
                <label class="form-label font-w600">Commentaire libre <span class="text-muted fw-normal">(optionnel)</span></label>
                <textarea name="commentaire" class="form-control" rows="4" maxlength="1000"
                    placeholder="Partagez vos remarques, suggestions ou observations...">{{ old('commentaire') }}</textarea>
                <div class="text-muted fs-12 mt-1 text-end" id="charCount">0 / 1000</div>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg px-5">
                <i class="lni lni-checkmark-circle me-2"></i>Soumettre mon évaluation
            </button>
        </div>
    </form>

</div>
</div>
@endsection

@push('scripts')
<script>
const LABELS = ['', 'Très insuffisant', 'Insuffisant', 'Correct', 'Bien', 'Excellent'];

function updateLabel(i, val, label) {
    const el = document.getElementById('val-' + i);
    el.textContent = val + ' — ' + label;
    el.className = 'badge fs-12 ' + ['','badge-danger','badge-warning','badge-info','badge-primary','badge-success'][val];
}

// Restaurer les labels si old() présent
document.querySelectorAll('input[type=radio]:checked').forEach(input => {
    const parts = input.name.match(/\[(\d+)\]/);
    if (parts) updateLabel(parseInt(parts[1]), parseInt(input.value), LABELS[parseInt(input.value)]);
});

// Compteur commentaire
const ta = document.querySelector('textarea[name=commentaire]');
const cc = document.getElementById('charCount');
if (ta && cc) {
    cc.textContent = ta.value.length + ' / 1000';
    ta.addEventListener('input', () => cc.textContent = ta.value.length + ' / 1000');
}
</script>
@endpush
