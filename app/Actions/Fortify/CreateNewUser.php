<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        $isEnseignant = ($input['role'] ?? '') === 'enseignant';
        $isDirecteur  = ($input['role'] ?? '') === 'directeur';

        Validator::make($input, [
            'prenom'       => ['required', 'string', 'max:255'],
            'nom'          => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'role'         => ['required', 'in:directeur,enseignant'],
            // Enseignant : plusieurs annexes obligatoires
            'annexe_ids'   => $isEnseignant ? ['required', 'array', 'min:1'] : ['nullable'],
            'annexe_ids.*' => $isEnseignant ? ['exists:annexes,id'] : ['nullable'],
            'password'     => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'prenom'   => $input['prenom'],
            'nom'      => $input['nom'],
            'name'     => $input['prenom'] . ' ' . $input['nom'],
            'email'    => $input['email'],
            'role'     => $input['role'],
            'password' => Hash::make($input['password']),
            // university_id / annexe_id uniquement pour directeur (géré ailleurs)
        ]);

        // Attacher les annexes pour l'enseignant
        if ($isEnseignant && ! empty($input['annexe_ids'])) {
            $user->annexes()->attach($input['annexe_ids']);
        }

        return $user;
    }
}
