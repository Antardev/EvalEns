<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Annexe extends Model
{
    protected $fillable = [
        'university_id', 'nom', 'adresse', 'ville', 'pays', 'email', 'telephone',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function gestionnaire(): HasOne
    {
        return $this->hasOne(User::class, 'annexe_id')->where('role', 'gestionnaire');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'annexe_id');
    }

    public function enseignants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enseignant_annexes')
                    ->where('role', 'enseignant');
    }
}
