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
     */
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@ontech.co.zm',
            'password' => Hash::make('Admin.1234'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create agents
        $agents = [
            [
                'name' => 'Wezi Munthali',
                'email' => 'wezi@ontech.co.zm',
                'role' => 'agent',
            ],
            [
                'name' => 'Prisca Chileshe',
                'email' => 'prisca@ontech.co.zm',
                'role' => 'agent',
            ],
            [
                'name' => 'Mwabafu Mututwa',
                'email' => 'mwabafu@ontech.co.zm',
                'role' => 'agent',
            ],
        ];

        foreach ($agents as $agent) {
            User::create([
                'name' => $agent['name'],
                'email' => $agent['email'],
                'password' => Hash::make('Admin.1234'),
                'role' => $agent['role'],
                'email_verified_at' => now(),
            ]);
        }

        // Create staff user
        User::create([
            'name' => 'Staff User',
            'email' => 'staff@ontech.co.zm',
            'password' => Hash::make('Admin.1234'),
            'role' => 'staff',
            'email_verified_at' => now(),
        ]);
    }
}
