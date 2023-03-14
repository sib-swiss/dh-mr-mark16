<?php

namespace App;

use Illuminate\Support\Collection;

class Nakala
{
    public static function getMeta(array $content, string $key): string|null
    {
        $firstMeta = self::getMetas($content, $key)->first();

        if (isset($firstMeta['value']) && is_string($firstMeta['value'])) {
            return $firstMeta['value'];
        }

        if (isset($firstMeta['value']['givenname']) && isset($firstMeta['value']['surname'])) {
            return $firstMeta['value']['givenname'].' '.$firstMeta['value']['surname'];
        }

        return 'NOT FOUND: '.$key;
    }

    public static function getMetas(array $content, string $key): Collection
    {
        if (! isset($content['metas'])) {
            return collect();
        }

        return collect($content['metas'])
            ->filter(function ($meta) use ($key) {
                return str_ends_with($meta['propertyUri'], '#'.$key)
                    || str_ends_with($meta['propertyUri'], '/'.$key);
            });
    }
}
