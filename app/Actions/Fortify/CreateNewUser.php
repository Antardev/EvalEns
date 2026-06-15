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
        $needsUniversity = ($input['role'] ?? '') === 'enseignant';

        Validator::make($input, [
            'prenom'        => ['required', 'string', 'max:255'],
            'nom'           => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'role'          => ['required', 'in:directeur,enseignant'],
            'university_id' => $needsUniversity
                ? ['required', 'exists:universities,id']
                : ['nullable'],
            'annexe_id'     => $needsUniversity
                ? ['required', 'exists:annexes,id']
                : ['nullable'],
            'password'      => $this->passwordRules(),
        ])->validate();

        return User::create([
            'prenom'        => $input['prenom'],
            'nom'           => $input['nom'],
            'name'          => $input['prenom'] . ' ' . $input['nom'],
            'email'         => $input['email'],
            'role'          => $input['role'],
            'university_id' => $needsUniversity ? ($input['university_id'] ?? null) : null,
            'annexe_id'     => $needsUniversity ? ($input['annexe_id'] ?? null) : null,
            'password'      => Hash::make($input['password']),
        ]);
    }
}
