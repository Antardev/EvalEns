<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class University extends Model
{
    protected $fillable = [
        'nom', 'acronyme',
        'email', 'telephone', 'site_web',
        'statut', 'directeur_id',
        'motif_rejet', 'validee_at', 'validee_par',
    ];

    protected $casts = [
        'validee_at' => 'datetime',
    ];

    public function directeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'directeur_id');
    }

    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validee_par');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function isEnAttente(): bool { return $this->statut === 'en_attente'; }
    public function isActive(): bool    { return $this->statut === 'active'; }
    public function isRejetee(): bool   { return $this->statut === 'rejetee'; }
}
