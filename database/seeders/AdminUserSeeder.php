<?php

namespace Database\Seeders;

Use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kalau sudah ada user, tidak usah buat apa-apa
        if (User::count() > 0) {
            return;
        }

        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@example.com',
            'role'     => 'admin',
            'password' => Hash::make('password123'), // ganti kalau mau
        ]);
    }
}
