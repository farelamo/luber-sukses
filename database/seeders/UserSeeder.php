<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::create([
            'username' => 'admin2024',
            'name' => 'Admin',
            'password' => bcrypt('Rahasi4'),
        ]);
    }
}
