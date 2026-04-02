<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Admin',
                'email'    => 'admin@gmail.com',
                'password' => Hash::make('password'),
            ],
            [
                'name'     => 'Abdulloh Karimov',
                'email'    => 'abdulloh@gmail.com',
                'password' => Hash::make('password'),
            ],
            [
                'name'     => 'Malika Yusupova',
                'email'    => 'malika@gmail.com',
                'password' => Hash::make('password'),
            ],
            [
                'name'     => 'Bobur Toshmatov',
                'email'    => 'bobur@gmail.com',
                'password' => Hash::make('password'),
            ],
            [
                'name'     => 'Nilufar Hasanova',
                'email'    => 'nilufar@gmail.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(['email' => $user['email']], $user);
        }

        $this->command->info('✅ ' . count($users) . ' ta foydalanuvchi yaratildi!');
        $this->command->info('📧 Admin: admin@gmail.com | 🔑 Parol: password');
    }
}
