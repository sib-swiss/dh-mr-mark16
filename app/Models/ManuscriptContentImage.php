<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
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

    protected $casts = [
        'content' => 'array',
    ];

    public function getCopyrightFontSize()
    {
        if (! $this->content) {
            return '';
        }

        if (isset($this->content['fontsize'])) {
            return $this->content['fontsize'];
        }

        [$width, $height] = getimagesize($this->getFirstMedia()->getPath());

        $fontSize = 12;
        if ($width > 1500) {
            $fontSize = 24;
        } elseif ($width > 1000) {
            $fontSize = 18;
        }

        return $fontSize;
    }

    public function imageWithCopyright()
    {

        $media = $this->getFirstMedia();

        $text = isset($this->content['copyright']) ? trim($this->content['copyright']) : '';
        if (! $text) {
            return $media->getPath();
        }

        $filePath = 'images/'.$media->id.'_'.$media->file_name;

        $storage = Storage::disk('public');
        if ($storage->exists($filePath)) {
            return $storage->path($filePath);
        }

        $image = Image::make($media->getPath());
        $image->rectangle(0, 0, $image->width(), 100, function ($draw) {
            $draw->background('rgba(255, 255, 255, 0.5)');
        });

        $image->text(
            $text,
            $image->width() - 10,
            $this->getCopyrightFontSize() + 10,
            function ($font) {
                $font->file(resource_path('fonts/GentiumBasic-Regular.ttf'));
                $font->size($this->getCopyrightFontSize());
                $font->color('#000');
                $font->align('right');
            }
        );

        Storage::disk('public')
            ->put(
                $filePath,
                $image->encode()
            );
        Storage::setVisibility($filePath, 'public');

        return $storage->path($filePath);
    }

    public function identifier(): Attribute
    {
        return Attribute::make(
            get: fn () => (string) $this->getFirstMedia()->id.'__'.$this->getFirstMedia()->file_name,
        );
    }
}
