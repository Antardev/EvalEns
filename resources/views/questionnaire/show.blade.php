@extends('layouts.questionnaire')

@section('title', $lien->titre)

@section('content')
<div class="row justify-content-center">
<div class="col-xl-7 col-lg-8 col-md-10 col-12">

    {{-- En-tête questionnaire --}}
    <div class="mb-3">
        <p class="fs-13 text-muted mb-0" style="line-height:1.6;">
            <i class="lni lni-lock-alt text-success me-1"></i>
            Vos réponses concernant l'enseignant  de {{ $lien->matiere }}  Mr/Mdme {{ $lien->enseignant->prenom }} {{ $lien->enseignant->nom }} sont <strong>strictement anonymes</strong> — votre identité n'est transmise ni à l'enseignant ni à l'administration. Évaluez chaque critère : <strong>20%, 40%, 60%, 80%</strong> ou <strong>100%</strong>.
        </p>
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

        {{-- Critères (toujours depuis la BD) --}}
        @foreach($criteres as $i => $critere)
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <h6 class="font-w600 mb-0">{{ $i + 1 }}. {{ $critere->nom }}</h6>
                    <span class="badge badge-light text-dark fs-12" id="val-{{ $i }}">—</span>
                </div>
                @if(!empty($critere->description))
                    <p class="text-muted fs-12 mb-3">{{ $critere->description }}</p>
                @endif

                <div class="star-group mb-1" role="group" aria-label="{{ $critere->nom }}">
                    @foreach([1 => '20%', 2 => '40%', 3 => '60%', 4 => '80%', 5 => '100%'] as $val => $label)
                        <div>
                            <input type="radio" name="scores[{{ $i }}]" id="s{{ $i }}_{{ $val }}"
                                value="{{ $val }}" {{ old("scores.$i") == $val ? 'checked' : '' }}
                                onchange="updateLabel({{ $i }}, {{ $val }}, '{{ $label }}')">
                            <label class="star-label" for="s{{ $i }}_{{ $val }}" title="{{ $label }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
                <div class="score-legend mt-1">
                    <span>20% — Très insuffisant</span>
                    <span>100% — Excellent</span>
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
const LABELS = ['', '20%', '40%', '60%', '80%', '100%'];

function updateLabel(i, val, label) {
    const el = document.getElementById('val-' + i);
    el.textContent = label;
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
