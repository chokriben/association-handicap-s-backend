<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Super Admin if it doesn't exist
        User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Atef BouBaker',
                'email' => 'atef.boubaker2000@gmail.com',
                'password' => Hash::make('atef.boubaker2000@gmail.com'), // Replace with a secure password
                'nom_association' => 'Super Admin Org',
                'type_organisation' => 'Organisme',
                'telephone' => '1234567890',
                'role' => 'super_admin',
                'status' => 'approved',
            ]
        );
    }
}