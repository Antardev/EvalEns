<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmploiDuTemps extends Model
{
    protected $table = 'emplois_du_temps';

    protected $fillable = ['annexe_id', 'semaine', 'statut'];

    protected $casts = ['semaine' => 'date'];

    public function annexe(): BelongsTo
    {
        return $this->belongsTo(Annexe::class);
    }

    public function creneaux(): HasMany
    {
        return $this->hasMany(Creneau::class)->orderBy('jour')->orderBy('heure_debut');
    }

    public function isPublie(): bool
    {
        return $this->statut === 'publie';
    }
}
