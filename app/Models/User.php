<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'prenom', 'nom', 'name',
        'email', 'role', 'password',
        'university_id', 'annexe_id', 'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    /** Annexe unique (gestionnaire, directeur). */
    public function annexe(): BelongsTo
    {
        return $this->belongsTo(Annexe::class);
    }

    /** Annexes multiples (enseignant — many-to-many). */
    public function annexes(): BelongsToMany
    {
        return $this->belongsToMany(Annexe::class, 'enseignant_annexes');
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }

    public function isSuperAdmin(): bool    { return $this->role === 'superadmin'; }
    public function isDirecteur(): bool     { return $this->role === 'directeur'; }
    public function isEnseignant(): bool    { return $this->role === 'enseignant'; }
    public function isGestionnaire(): bool  { return $this->role === 'gestionnaire'; }

    public function dashboardRoute(): string
    {
        return match($this->role) {
            'superadmin'   => route('superadmin.dashboard'),
            'directeur'    => route('adminuniversity.dashboard'),
            'gestionnaire' => route('gestionnaire.dashboard'),
            'enseignant'   => route('teacher.dashboard'),
            default        => route('home'),
        };
    }
}
