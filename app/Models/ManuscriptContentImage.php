<?php

namespace App\Models;

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
}
