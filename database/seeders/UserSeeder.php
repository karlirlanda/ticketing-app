<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'department_id' => 1,
            'contact_number' => '1234567890',
            'name' => 'Admin User',
            'email' => 'admin@mail.com',
            'role' => 1, //1 - admin, 2 - user
            'status' => 1, //1 - active, 2 - inactive
            'password' => Hash::make('password'), // Use bcrypt for hashing
        ]);

        User::create([
            'username' => 'user',
            'department_id' => 1,
            'contact_number' => '2234567890',
            'name' => 'User User',
            'email' => 'user@mail.com',
            'role' => 2, //1 - admin, 2 - user
            'status' => 1, //1 - active, 2 - inactive
            'password' => Hash::make('password'), // Use bcrypt for hashing
        ]);
    }
}
