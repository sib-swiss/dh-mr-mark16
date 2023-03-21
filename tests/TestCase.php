<?php

namespace Tests;

use App\Models\Manuscript;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    protected function createManuscript(): Manuscript
    {
        $url = 'https://api.nakala.fr/datas/11280/4242f209';
        $manuscript = Manuscript::syncFromNakalaUrl($url);

        return $manuscript;
    }
}
