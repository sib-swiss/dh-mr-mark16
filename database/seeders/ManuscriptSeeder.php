<?php

namespace Database\Seeders;

use App\Models\Manuscript;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ManuscriptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        config(['database.connections.from' => [
            'driver' => 'sqlite',
            'database' => storage_path('/app/from/database.sqlite'),
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ]]);

        $manuscripts = DB::connection('from')->table('manuscripts')->get();
        foreach ($manuscripts as $manuscripData) {
            Manuscript::create((array) $manuscripData);
        }
    }
}
