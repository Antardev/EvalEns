<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LienQuestionnaire extends Model
{
    protected $table = 'liens_questionnaires';

    protected $fillable = [
        'token', 'gestionnaire_id', 'annexe_id', 'classe', 'matiere',
        'enseignant_id', 'titre', 'questions', 'statut', 'expire_at',
    ];

    protected $casts = [
        'questions' => 'array',
        'expire_at' => 'datetime',
    ];

    public function gestionnaire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gestionnaire_id');
    }

    public function annexe(): BelongsTo
    {
        return $this->belongsTo(Annexe::class);
    }

    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }

    public function reponses(): HasMany
    {
        return $this->hasMany(ReponseQuestionnaire::class);
    }

    public function isActif(): bool
    {
        if ($this->statut !== 'actif') return false;
        if ($this->expire_at && $this->expire_at->isPast()) return false;
        return true;
    }

    public function urlPublique(): string
    {
        return route('questionnaire.show', $this->token);
    }

    /** Génère un token unique de 40 caractères. */
    public static function genererToken(): string
    {
        do {
            $token = Str::random(40);
        } while (static::where('token', $token)->exists());

        return $token;
    }
}
