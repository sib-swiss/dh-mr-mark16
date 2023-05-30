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

        $this->call([
            UserSeeder::class,
            ManuscriptSeeder::class,
            ManuscriptContentSeeder::class,
        ]);
    }
}
