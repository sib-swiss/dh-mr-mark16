<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ManuscriptContent extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Manuscript, \App\Models\ManuscriptContent>
     */
    public function manuscript()
    {
        return $this->belongsTo(Manuscript::class);
    }

    // public function image(): ?Media
    // {
    //     return $this->getFirstMedia();
    //     $url = $this->getFirstMediaUrl();

    //     return $this->media;
    // }
}
