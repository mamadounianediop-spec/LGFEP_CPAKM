<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Admin CPAKM',
            'email' => 'admin@cpakm.sn',
            'password' => bcrypt('admin123'),
            'role' => 'administrateur',
            'telephone' => '77 123 45 67',
            'adresse' => 'Dakar, Sénégal',
            'actif' => true,
            'email_verified_at' => now(),
        ]);
    }
}
