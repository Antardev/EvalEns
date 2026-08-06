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
        'enseignant_id', 'titre', 'questions', 'statut', 'debut_at', 'expire_at',
    ];

    protected $casts = [
        'questions' => 'array',
        'debut_at'  => 'datetime',
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

    /**
     * Le lien est-il actuellement accessible ?
     * Vérifie le statut, la date d'ouverture et la date d'expiration.
     */
    public function isActif(): bool
    {
        if ($this->statut !== 'actif') return false;
        if ($this->debut_at && $this->debut_at->isFuture()) return false;
        if ($this->expire_at && $this->expire_at->isPast()) return false;
        return true;
    }

    /** Le lien est programmé pour une ouverture future (pas encore accessible). */
    public function estProgramme(): bool
    {
        return $this->statut === 'actif'
            && $this->debut_at
            && $this->debut_at->isFuture();
    }

    /** Le lien a dépassé sa date d'expiration. */
    public function estExpire(): bool
    {
        return $this->expire_at && $this->expire_at->isPast();
    }

    /**
     * Message expliquant pourquoi le lien n'est pas accessible, ou null s'il l'est.
     */
    public function messageIndisponibilite(): ?string
    {
        if ($this->statut !== 'actif') {
            return 'Ce questionnaire est actuellement fermé.';
        }

        if ($this->estProgramme()) {
            return 'Ce questionnaire ouvrira le ' . $this->debut_at->format('d/m/Y à H:i') . '.';
        }

        if ($this->estExpire()) {
            return 'Ce questionnaire a expiré le ' . $this->expire_at->format('d/m/Y à H:i') . '.';
        }

        return null;
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
