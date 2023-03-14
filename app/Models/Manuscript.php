<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Manuscript extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'content' => 'array',
    ];

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

    /**
     * The model's time entries.
     *
     * @return HasMany<ManuscriptContentImage>
     */
    public function images()
    {
        return $this->hasMany(ManuscriptContentImage::class)
            ->whereIn('extension', ['jpg', 'jpeg'])
            ->where('name', 'NOT LIKE', '%partner%')
            ->orderBy('name');
    }

    /**
     * The model's time entries.
     *
     * @return HasMany<ManuscriptContentImage>
     */
    public function partners()
    {
        return $this->hasMany(ManuscriptContentImage::class)->where('name', 'like', '%partner%');
    }

    public function getDisplayname(): string
    {
        return $this->getMeta('bibliographicCitation');
    }

    public function getMeta(string $key): string|null
    {
        $firstMeta = $this->getMetas($key)->first();

        if (isset($firstMeta['value']) && is_string($firstMeta['value'])) {
            return $firstMeta['value'];
        }

        if (isset($firstMeta['value']['givenname']) && isset($firstMeta['value']['surname'])) {
            return $firstMeta['value']['givenname'].' '.$firstMeta['value']['surname'];
        }

        return 'NOT FOUND: '.$key;
    }

    public function getLangExtended(): string
    {
        return 'todo';
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

        if (! isset($content['metas'])) {
            return collect();
        }

        return collect($content['metas'])
            ->filter(function ($meta) use ($key) {
                return str_ends_with($meta['propertyUri'], '#'.$key)
                    || str_ends_with($meta['propertyUri'], '/'.$key);
            });
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
