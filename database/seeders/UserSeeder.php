<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id_card' => '116511111',
            'name' =>  'Trihadi',
            'email' => 'trihadi17@gmail.com',
            'password' => Hash::make('password123'),
            'address' => 'Jalan Serayu',
            'gender' => 'Male',
            'phone_number' => '0895603075970',
            'profile_picture' => 'profile.jpg',
            'role' => 'Admin',
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }
}
