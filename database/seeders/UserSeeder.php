<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create one admin user with password: to change manually after
        User::create([
            'name' => 'Silvano',
            'email' => 'silvano.alda@sib.swiss',
            'password' => Hash::make('password'),
        ]);
    }
}
