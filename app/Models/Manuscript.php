<?php

namespace App\Models;

use App\Nakala;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Manuscript extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'content' => 'array',
    ];

    public function syncFromNakala(): array
    {
        $manuscript = $this->syncFromNakalaUrl($this->url);
        if (! $manuscript) {
            dd($manuscript);
        }

        return ['version' => $manuscript->content['version']];

    }

    public function partners()
    {
        return $this->morphMany(config('media-library.media_model'), 'model')
            ->where('collection_name', 'partners');
    }

    public static function syncFromNakalaUrl(string $url = null): Manuscript|null
    {
        if (! $url) {
            return $url;
        }

        $jsonContent = Http::get($url)->json();
        // dd($jsonContent);
        $manuscriptName = strtoupper(str_replace(' ', '', Nakala::getMeta($jsonContent, 'bibliographicCitation')));
        $manuscript = self::firstWhere('name', $manuscriptName);
        if ($manuscript) {
            $manuscript->update([
                'url' => $url,
                'content' => $jsonContent,
                'temporal' => Nakala::getMeta($jsonContent, 'temporal'),
            ]);
        } else {
            $manuscript = self::create([
                'name' => $manuscriptName,
                'url' => $url,
                'content' => $jsonContent,
                'temporal' => Nakala::getMeta($jsonContent, 'temporal'),
            ]);
        }

        $contentNames = [];
        foreach ($jsonContent['files'] as $nakalaFileData) {
            $nakala_parsed_url = parse_url($manuscript->url); // ex. 'https://api.nakala.fr/datas/10.34847/nkl.6f83096n'
            $nakala_download_url = $nakala_parsed_url['scheme'].'://'.$nakala_parsed_url['host'].str_replace('datas', 'data', $nakala_parsed_url['path']);
            $url = $nakala_download_url.'/'.$nakalaFileData['sha1'];
            $contentNames[] = $nakalaFileData['name'];

            $manuscriptContent = ManuscriptContent::updateOrCreate(
                ['manuscript_id' => $manuscript->id, 'name' => $nakalaFileData['name']],
                [
                    'extension' => strtolower($nakalaFileData['extension']),
                    'url' => $url,
                    'content' => file_get_contents($url),
                ]
            );
        }

        // logger()->info('Manuscript '.$manuscript->name, ['contentNames'=> $contentNames]);
        $manuscript->folios()->whereNotIn('name', $contentNames)->get()->each->delete();
        $manuscript->contentsHtml()->whereNotIn('name', $contentNames)->get()->each->delete();

        return $manuscript;
    }

    /**
     * The model's time entries.
     *
     * @return HasMany<ManuscriptContent>
     */
    public function contents()
    {
        return $this->hasMany(ManuscriptContent::class);
    }

    /**
     * The model's time entries.
     *
     * @return HasMany<ManuscriptContentMeta>
     */
    public function folios()
    {
        return $this->hasMany(ManuscriptContentMeta::class)->where('extension', 'xml')->orderBy('name');
    }

    /**
     * return array of manuscript's html contents
     *
     * @return HasMany<ManuscriptContentHtml>
     */
    public function contentsHtml()
    {
        return $this->hasMany(ManuscriptContentHtml::class)
            ->whereIn('extension', ['html', 'htm'])
            ->orderBy('name');
    }

    // /**
    //  * The model's time entries.
    //  *
    //  */
    // public function images()
    // {
    //     return $this->folios->each->getFirstMedia();
    // }

    public function getDisplayname(): string
    {
        return $this->getMeta('bibliographicCitation');
    }

    public function getMeta(string $key): string|null
    {
        $content = is_array($this->content) ? $this->content : json_decode((string) $this->content, true);

        return Nakala::getMeta($content, $key);
    }

    public function getLangExtended(): string
    {
        $metaLanguage = $this->getMeta('language');

        if (config("manuscript.languages.{$metaLanguage}.name")) {
            return config("manuscript.languages.{$metaLanguage}.name");
        }

        return $metaLanguage;
    }

    /**
     * Return Manuscript language code
     * ex. grc for Ancient Greek
     *
     * @return string
     */
    public function getLangCode()
    {
        $metaLanguage = $this->getMeta('language');

        return $metaLanguage;

        if (isset($this->f3->get('MR_CONFIG')->languages->{$metaLanguage})) {
            return $metaLanguage;
        }

        foreach ($this->f3->get('MR_CONFIG')->languages as $langCode => $langObj) {
            if ($langObj->name == $metaLanguage) {
                return $langCode;
            }
        }

        // not found in congig.json languages
        return null;
    }

    public function getMetas(string $key): Collection
    {
        $content = is_array($this->content) ? $this->content : json_decode((string) $this->content, true);

        return Nakala::getMetas($content, $key);
    }

    // * https://iiif.io/api/presentation/3.0/#52-manifest
    // *
    // * Manuscrio V2.1 Ex.: https://mr-mark16.sib.swiss/api/iiif/2-1/GA05/manifest
    // * ex. https://iiif.io/api/cookbook/recipe/0009-book-1/manifest.json
    // https://iiif.io/api/presentation/3.0/#52-manifest
    public function manifest(): Attribute
    {
        $manifest = [];
        $manifest['@context'] = 'http://iiif.io/api/presentation/3/context.json';
        $manifest['type'] = 'Manifest';
        $manifest['id'] = url("/iiif/{$this->name}/manifest.json");
        $manifest['metadata'] = [];
        $creator = $this->getMeta('creator');
        if ($creator) {
            $manifest['metadata'][] = [
                'label' => ['en' => ['Author']],
                'value' => ['none' => [$creator]],
            ];
        }

        $provenance = $this->getMeta('provenance');
        if ($provenance) {
            $manifest['metadata'][] = [
                'label' => ['en' => ['Published']],
                'value' => [
                    $this->getMeta('language') => [$provenance],
                ],
            ];
        }

        $manifest['label'] = (object) [
            'en' => [
                $this->getMeta('bibliographicCitation'),
            ],
        ];
        $manifest['behavior'] = [
            'individuals',
        ];

        $items = [];
        foreach ($this->folios as $folio) {
            $manifest['items'][] = $folio->canvas();
        }

        return Attribute::make(
            get: fn () => (object) $manifest,
        );
    }
}
