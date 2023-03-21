<?php

use App\Models\Manuscript;
use App\Models\ManuscriptContent;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

it('manuscript_seeder', function () {
    if (! File::exists(storage_path('/app/from/database.sqlite'))) {
        return;
    }
    Artisan::call('db:seed');
    $this->assertTrue(Manuscript::count() > 0);
    $this->assertTrue(ManuscriptContent::count() > 0);
});

it('manuscript_from_nakala_url', function () {
    // Storage::deleteDirectory('public');
    $url = 'https://api.nakala.fr/datas/11280/4242f209';
    $manuscript = Manuscript::syncFromNakalaUrl($url);
    $this->assertTrue(Manuscript::count() > 0);
    $this->assertSame('GA019', $manuscript->name);
    $this->assertSame('20019', $manuscript->temporal);
    $this->assertCount(8, $manuscript->contents);
    $this->assertCount(4, $manuscript->folios);
});

it('manuscript get meta', function () {
    // config(['database.connections.sqlite.database' => database_path('database.sqlite')]);
    // dd(config('database.connections.sqlite'));
    // DB::purge('sqlite');
    $url = 'https://api.nakala.fr/datas/11280/4242f209';
    $manuscript = Manuscript::syncFromNakalaUrl($url);
    $this->assertSame('GA 019', $manuscript->getMeta('bibliographicCitation'));
    $metaCreators = collect($manuscript->getMetas('creator'))->pluck('value.fullName');
    $this->assertContains('Mina Monier', $metaCreators);
    $this->assertContains('Institut für neutestamentliche Textforschung (Münster)', $metaCreators);
    $this->assertSame('Bibliothèque nationale de France', $manuscript->getMeta('provenance'));
});

it('missing features', function () {
    [
        'frontend missing features' => [
            'main menu pages',
            'manuscript show',
        ],
        'dev2/prod2 servers',
        'pipeline',
        'admin filament',
    ];
})->todo();
