<?php

namespace Tests\Feature;

use App\Models\Manuscript;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ManuscriptTest extends TestCase
{
    use RefreshDatabase;

    /**
     * import manuscript from nakala URL
     */
    public function test_manuscript_seeder(): void
    {
        Artisan::call('db:seed');
        $this->assertTrue(Manuscript::count() > 0);
    }
}
