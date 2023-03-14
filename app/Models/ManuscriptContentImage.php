<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ManuscriptContentImage extends ManuscriptContent implements HasMedia
{
    use InteractsWithMedia;

    // public function image(): ?Media
    // {
    //     return $this->getFirstMedia();
    //     $url = $this->getFirstMediaUrl();

    //     return $this->media;
    // }

    protected $table = 'manuscript_contents';

    public function identifier(): Attribute
    {
        return Attribute::make(
            get: fn () => (string) $this->getFirstMedia()->id.'__'.$this->getFirstMedia()->file_name,
        );
    }
}
