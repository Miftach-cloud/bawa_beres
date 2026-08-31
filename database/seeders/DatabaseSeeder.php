<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ServiceSeeder::class,
        ]);

        $defaultUsers = [
            [
                'email' => 'owner@bawaberes.id',
                'name' => 'Owner Bawa Beres',
                'role' => UserRole::OWNER,
                'password' => Hash::make('password'),
            ],
            [
                'email' => 'admin@bawaberes.id',
                'name' => 'Admin Operasional',
                'role' => UserRole::ADMIN,
                'password' => Hash::make('password'),
            ],
            [
                'email' => 'operation@bawaberes.id',
                'name' => 'Tim Gudang & Lapangan',
                'role' => UserRole::OPERATION,
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($defaultUsers as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                $u
            );
        }
    }
}
