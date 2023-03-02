<?php

namespace Database\Seeders;

use App\Models\ManuscriptContent;
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
            $manuscriptContent = ManuscriptContent::create((array) $manuscriptContentData);

            $pathToFile = '';
            $manuscriptPath = storage_path("/app/from/manuscripts/{$manuscriptContent->manuscript->name}");
            $filename = pathinfo($manuscriptContent->name, PATHINFO_FILENAME);
            if (str_contains($filename, '_ENG')
            || str_contains($filename, '_FRA')
            || str_contains($filename, '_GER')) {
                continue;
            }

            if (str_starts_with($filename, 'partner-')) {
                $filePartner = $manuscriptPath.'/'.$filename.'.png';
                if (File::exists($filePartner)) {
                    $pathToFile = $filePartner;
                } else {
                    $filePartner = $manuscriptPath.'/'.$filename.'.jpg';
                    if (File::exists($filePartner)) {
                        $pathToFile = $filePartner;
                    }
                }
            } else {
                $pathToFile = $this->searchImage($manuscriptPath, $filename);

                if (! $pathToFile) {
                    $pathToFile = $this->searchImage($manuscriptPath.'/'.$filename, $filename);
                }
            }

            if (! $pathToFile) {
                dd([
                    $manuscriptContent->manuscript->name,
                    $manuscriptContent->name,
                    $pathToFile,
                ]);
            }

            if ($pathToFile) {
                $addMedia = $manuscriptContent->addMedia($pathToFile)
                    ->preservingOriginal()
                    ->toMediaCollection();
            }
        }
    }

    private function searchImage(string $folder, $filename): string
    {
        $fileOriginal = $folder.'/'.$filename.'_original.jpg';
        if (File::exists($fileOriginal)) {
            return  $fileOriginal;
        }
        $fileOriginal = $folder.'/'.$filename.'_original.jpeg';
        if (File::exists($fileOriginal)) {
            return  $fileOriginal;
        }
        $fileNotOriginal = $folder.'/'.$filename.'.jpg';
        if (File::exists($fileNotOriginal)) {
            return $fileNotOriginal;
        }

        return '';
    }
}
