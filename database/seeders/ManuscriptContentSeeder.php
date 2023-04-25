<?php

namespace Database\Seeders;

use App\Models\ManuscriptContent;
use App\Models\ManuscriptContentImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ManuscriptContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manuscriptContents = DB::connection('from')->table('manuscript_contents')->get();
        ManuscriptContent::truncate();
        Storage::deleteDirectory('public');
        foreach ($manuscriptContents as $manuscriptContentData) {
            $pathinfo = pathinfo($manuscriptContentData->name);

            $manuscriptContent = in_array($pathinfo['extension'], ['jpg', 'jpeg', 'png'])
                ? ManuscriptContentImage::create((array) $manuscriptContentData)
                : ManuscriptContent::create((array) $manuscriptContentData);

            $manuscriptPath = storage_path("/app/from/manuscripts/{$manuscriptContent->manuscript->name}");

            $pathToFile = $this->searchImage($manuscriptPath, $manuscriptContent->name);
            if ($pathToFile) {
                $addMedia = $manuscriptContent->addMedia($pathToFile)
                    ->preservingOriginal()
                    ->toMediaCollection();
            }
        }
    }

    private function searchImage(string $folder, $filename): string
    {
        if (
            str_contains($filename, '_ENG')
            || str_contains($filename, '_FRA')
            || str_contains($filename, '_GER')
        ) {
            return '';
        }

        if (str_starts_with($filename, 'partner-')) {
            $filePartner = $folder.'/'.$filename;
            if (File::exists($filePartner)) {
                $pathToFile = $filePartner;
            } else {
                $filePartner = $folder.'/'.$filename;
                if (File::exists($filePartner)) {
                    $pathToFile = $filePartner;
                }
            }

            if (! $pathToFile) {
                dd([
                    $folder,
                    $filename,
                ]);
            }

            return $pathToFile;
        }

        $pathinfo = pathinfo($filename);
        // dd($pathinfo);
        $fileOriginal = $folder.'/'.$pathinfo['filename'].'_original.'.$pathinfo['extension'];
        if (File::exists($fileOriginal)) {
            return $fileOriginal;
        }
        $file = $folder.'/'.$filename;
        if (File::exists($file)) {
            return $file;
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
