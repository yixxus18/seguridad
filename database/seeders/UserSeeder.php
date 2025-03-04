<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Jesus',
            'email' => 'yisuskroom@gmail.com',
            'password' => Hash::make('password123'),
            'two_factor_enabled' => true, // Habilitar 2FA para pruebas
        ]);
    }
}
