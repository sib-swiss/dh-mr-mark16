<?php

namespace Database\Seeders;

use App\Models\Manuscript;
use App\Models\ManuscriptContent;
use App\Models\ManuscriptContentHtml;
use App\Models\ManuscriptContentMeta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
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
            $content = (bool) json_decode($manuscriptContentData->content)
                    ? json_decode($manuscriptContentData->content, true)
                    : $manuscriptContentData->content;

            if ($pathinfo['extension'] === 'xml') {
                $manuscriptContentData->content = $content;
                $manuscriptContentMeta = ManuscriptContentMeta::create((array) $manuscriptContentData);

            } elseif (in_array($pathinfo['extension'], ['html', 'htm'])) {
                $manuscriptContentData->content = $content;
                $manuscriptContentMeta = ManuscriptContentHtml::create((array) $manuscriptContentData);

            } elseif (in_array($pathinfo['extension'], ['jpg', 'jpeg', 'png'])) {

                if (str_starts_with($manuscriptContentData->name, 'partner-')) {
                    $manuscript = Manuscript::find($manuscriptContentData->manuscript_id);
                    $manuscriptPath = storage_path("/app/from/manuscripts/{$manuscript->name}");
                    $pathToFile = $this->searchImage($manuscriptPath, $manuscriptContentData->name);
                    if ($pathToFile) {
                        $addMedia = $manuscript->addMedia($pathToFile)
                            ->preservingOriginal()
                            ->withCustomProperties(['url' => $manuscriptContentData->url])
                            ->toMediaCollection('partners');
                        // dd(
                        //     $manuscript->id,
                        //     $pathToFile,
                        //     $addMedia,
                        // );
                    }
                } else {
                    $pathinfo = pathinfo($manuscriptContentData->name);
                    $manuscriptContentMeta = ManuscriptContentMeta::where('manuscript_id', $manuscriptContentData->manuscript_id)
                        ->whereRaw("REPLACE(name,'.xml','') =?", [str_replace(['.jpg', '.jpeg', '.png'], '', $pathinfo['basename'])])
                        ->first();
                    if (! $manuscriptContentMeta) {
                        // dd($pathinfo);
                        dd([
                            $manuscriptContentData,
                            $pathinfo,
                            ManuscriptContentMeta::where('manuscript_id', $manuscriptContentData->manuscript_id)->pluck('name'),

                        ]);
                    }
                    $manuscriptPath = storage_path("/app/from/manuscripts/{$manuscriptContentMeta->manuscript->name}");
                    $pathToFile = $this->searchImage($manuscriptPath, $manuscriptContentData->name);
                    if ($pathToFile) {
                        $addMedia = $manuscriptContentMeta->addMedia($pathToFile)
                            ->preservingOriginal()
                            ->withCustomProperties($content ? Arr::only($content, ['copyright', 'fontsize']) : [])
                            ->toMediaCollection();

                    }
                }

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
