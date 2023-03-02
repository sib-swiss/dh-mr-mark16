<?php

namespace Tests\Feature;

use App\Models\Manuscript;
use App\Models\ManuscriptContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ManuscriptTest extends TestCase
{
    use RefreshDatabase;

    /**
     * import manuscript from nakala URL
     */
    public function test_manuscript_seeder(): void
    {
        if (! File::exists(storage_path('/app/from/database.sqlite'))) {
            return;
        }
        Artisan::call('db:seed');
        $this->assertTrue(Manuscript::count() > 0);
        $this->assertTrue(ManuscriptContent::count() > 0);
    }

    public function test_manuscript_get_meta()
    {
        config(['database.connections.sqlite.database' => database_path('database.sqlite')]);
        // dd(config('database.connections.sqlite'));
        DB::purge('sqlite');
        $manuscript = Manuscript::firstWhere('name', 'GA05');
        $this->assertSame('http://localhost/storage/122/partner-1616426116.png', $manuscript->partners[0]->getFirstMediaUrl());
    }
}
