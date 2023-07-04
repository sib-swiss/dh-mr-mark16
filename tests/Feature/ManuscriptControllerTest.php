<?php

namespace Tests\Feature;

use App\Models\Manuscript;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManuscriptControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_list_published_manuscripts(): void
    {
        $url = 'https://api.nakala.fr/datas/11280/4242f209';
        $manuscript = Manuscript::syncFromNakalaUrl($url);

        $this->get('/')
            ->assertStatus(200)
            ->assertDontSee('GA019');

        $manuscript->update(['published' => 1]);
        $this->get('/')
            ->assertSee('GA019');
    }
}
