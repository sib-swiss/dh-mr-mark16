<?php

namespace Tests\Feature;

use App\Models\Manuscript;
use App\Models\ManuscriptContent;
use App\Models\ManuscriptContentImage;
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
        $manuscript = Manuscript::firstWhere('name', 'GA019');
        $this->assertSame('http://localhost/storage/55/partner-1616516171.png', $manuscript->partners[0]->getFirstMediaUrl());
        // $this->assertSame('GA 019', $manuscript->getMeta('bibliographicCitation'));
        $this->assertSame('Mina Monier', $manuscript->getMeta('creator'));
        $this->assertSame('Bibliothèque nationale de France', $manuscript->getMeta('provenance'));

    }

    public function test_seeder()
    {
        config(['database.connections.sqlite.database' => database_path('database.sqlite')]);
        // dd(config('database.connections.sqlite'));
        DB::purge('sqlite');

        $manuscriptContent = ManuscriptContentImage::find(676);

        $manuscriptPath = storage_path("/app/from/manuscripts/{$manuscriptContent->manuscript->name}");

        $pathToFile = $this->searchImage($manuscriptPath, $manuscriptContent->name);

        dd([
            // $manuscriptContent->manuscript->name,
            // $manuscriptContent->name,
            // $manuscriptContent->id,
            $manuscriptContent->getFirstMedia()->toArray(),
            $manuscriptContent->getimagesize()->width,
            // $manuscriptContent->getFirstMediaUrl(),
            // $pathToFile,
        ]);
    }

    private function searchImage(string $folder, $filename): string
    {
        $pathinfo = pathinfo($filename);
        // dd($pathinfo);
        $fileOriginal = $folder.'/'.$pathinfo['filename'].'_original.'.$pathinfo['extension'];
        if (File::exists($fileOriginal)) {
            return  $fileOriginal;
        }
        $file = $folder.'/'.$filename;
        if (File::exists($file)) {
            return  $file;
        }

        if (in_array($pathinfo['extension'], ['jpg', 'jpeg', 'png'])) {
            dd([
                $folder,
                $filename,
                'not found',
            ]);
        }

        return '';
    }
}
