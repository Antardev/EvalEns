<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile', ['user' => Auth::user()]);
    }

    public function updateInfo(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'prenom' => ['required', 'string', 'max:255'],
            'nom'    => ['required', 'string', 'max:255'],
            'email'  => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update([
            'prenom' => $data['prenom'],
            'nom'    => $data['nom'],
            'name'   => $data['prenom'] . ' ' . $data['nom'],
            'email'  => $data['email'],
        ]);

        return back()->with('success_info', 'Informations mises à jour avec succès.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success_password', 'Mot de passe mis à jour avec succès.');
    }
}
