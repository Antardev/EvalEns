<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@evalens.fr'],
            [
                'prenom'   => 'Super',
                'nom'      => 'Admin',
                'name'     => 'Super Admin',
                'email'    => 'superadmin@evalens.fr',
                'role'     => 'superadmin',
                'password' => Hash::make('Admin@1234'),
            ]
        );

        $this->command->info('SuperAdmin créé : superadmin@evalens.fr / Admin@1234');
    }
}
