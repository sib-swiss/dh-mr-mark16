<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        config(['database.connections.from' => [
            'driver' => 'sqlite',
            'database' => storage_path('/app/from/database.sqlite'),
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ]]);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            ManuscriptSeeder::class,
            ManuscriptContentSeeder::class,
        ]);
    }
}
