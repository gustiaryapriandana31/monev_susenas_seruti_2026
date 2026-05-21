<?php

namespace Database\Seeders;

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
        // User::factory(10)->create();

        $users = [
            [
                'name'     => 'Administrator IPDS',
                'username' => 'adminipds',
                'email'    => 'adminipds@bps.go.id',
                'password' => Hash::make('password'),
                'role'     => 'adminipds',
            ],
            [
                'name'     => 'Administrator Sosial',
                'username' => 'adminsosial',
                'email'    => 'adminsosial@bps.go.id',
                'password' => Hash::make('password'),
                'role'     => 'adminsosial',
            ],
            [
                'name'     => 'Super Administrator',
                'username' => 'superadmin',
                'email'    => 'superadmin@bps.go.id',
                'password' => Hash::make('password'),
                'role'     => 'superadmin',
            ],
        ];

        foreach ($users as $user) {
            User::factory()->create($user);
        }
    }
}
