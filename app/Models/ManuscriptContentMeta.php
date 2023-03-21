<?php

namespace App\Models;

class ManuscriptContentMeta extends ManuscriptContent
{
    protected $table = 'manuscript_contents';

    public function contentImage()
    {
        return $this->hasOne(ManuscriptContentImage::class, 'manuscript_id', 'manuscript_id')
            ->whereIn('extension', ['jpg', 'jpeg'])
            ->whereRaw("REPLACE(name,'.jpg','') =?", [str_replace('.xml', '', $this->name)]);
    }

    public function contentHtml()
    {
        return $this->hasOne(ManuscriptContentHtml::class, 'manuscript_id', 'manuscript_id')
            ->whereIn('extension', ['html', 'htm'])
            ->whereRaw(
                "REPLACE(REPLACE(name,'.html',''),'.htm','')=?",
                [str_replace('.xml', '', $this->name)]
            );
    }


    /**
     * return array of manuscript's folio additional languages
     *
     * @return array
     */
    public function contentsTranslations()
    {
        // return $this->manuscript->contentsHtml()

        // $name = str_replace('_Metadata', '', $this->name);
        // $parts1 = explode('/', $name);
        // if (count($parts1) == 1) {
        //     $parts2 = explode('.', $parts1[0]);

        //     return  substr($name, 0, -strlen(end($parts2)) - 1);
        // }

        // //  GA304_240r/GA304_240r_ENG.html
        // $parts2 = explode('_', $parts1[1]);
        // $parts3 = explode('_', $parts2[1]);
        // $parts4 = explode('.', $parts3[0]);

        // return $parts2[0] . '_' . $parts4[0];

        return  $this->hasMany(ManuscriptContentHtml::class, 'manuscript_id', 'manuscript_id')
            ->whereIn('extension', ['html', 'htm'])

            // this will not work in eager load (using with method)
            ->where('name', 'like', "{$this->getFolioName()}%")
            // ->where(function ($query) {
            //     // 'name', 'like', "{$this->getFolioName()}%")
            //     $query->whereColumn('name', 'like', 'name'); // str_replace('.xml', '', $this->name));
            // })

            ->where(function ($query) {
                $query->where('name', 'LIKE', "%_ENG.%")
                    ->orWhere('name', 'LIKE', "%_FRA.%")
                    ->orWhere('name', 'LIKE', "%_GER.%");
            });
    }

    /**
     * https://iiif.io/api/presentation/3.0/#53-canvas
     */
    public function canvas(): object
    {
        if (!$this->contentImage) {
            return (object) [];
        }
        $items = [];
        foreach ($this->contentImage->media as $media) {
            $getimagesize = getimagesize($media->getPath());
            $items[] = [
                'id' => url("/iiif/{$this->manuscript->name}/annotation/p000{$this->pageNumber}-image"), //"https://iiif.io/api/cookbook/recipe/0009-book-1/annotation/p0001-image",
                'type' => 'Annotation',
                'motivation' => 'painting',
                'body' => [
                    //{identifier}/{region}/{size}/{rotation}/{quality}.{format}
                    'id' => route('iiif.image.requests', [$media->id . '__' . $media->file_name, 'full', 'max', '0', 'default', 'jpg']), //"https://iiif.io/api/image/3.0/example/reference/59d09e6773341f28ea166e9f3c1e674f-gallica_ark_12148_bpt6k1526005v_f18/full/max/0/default.jpg",
                    'type' => 'Image',
                    'format' => $media->mime_type,
                    'height' => $getimagesize[1],
                    'width' => $getimagesize[0
                        // 'service' => [  // https://iiif.io/api/registry/services/
                        //     [
                        //         'id' => 'https://iiif.io/api/image/3.0/example/reference/59d09e6773341f28ea166e9f3c1e674f-gallica_ark_12148_bpt6k1526005v_f18',
                        //         'type' => 'ImageService3',
                        //         'profile' => 'level1',
                        //     ],
                    ],
                ],
                'target' => 'https://iiif.io/api/cookbook/recipe/0009-book-1/canvas/p1',
            ];
        }
        $canvas = [
            'id' => url("/iiif/{$this->manuscript->name}/canvas/p{$this->pageNumber}"),
            'type' => 'Canvas',
            // 'DEBUG_NAME' => $this->name,
            'label' => ['none' => [substr($this->name, 0, -4)]],
            'height' => 1000,
            'width' => 750,
            'items' => [
                [
                    'id' => url("/iiif/{$this->manuscript->name}/canvas/p{$this->pageNumber}/1"), //"https://iiif.io/api/cookbook/recipe/0009-book-1/page/p1/1",
                    'type' => 'AnnotationPage',
                    'items' => $items,
                ],
            ],
        ];
        // dd($canvas);

        //     "items": [
        //       {
        //         "id": "https://iiif.io/api/cookbook/recipe/0009-book-1/page/p1/1",
        //         "type": "AnnotationPage",
        //         "items": [
        //           {
        //             "id": "https://iiif.io/api/cookbook/recipe/0009-book-1/annotation/p0001-image",
        //             "type": "Annotation",
        //             "motivation": "painting",
        //             "body": {
        //               "id": "https://iiif.io/api/image/3.0/example/reference/59d09e6773341f28ea166e9f3c1e674f-gallica_ark_12148_bpt6k1526005v_f18/full/max/0/default.jpg",
        //               "type": "Image",
        //               "format": "image/jpeg",
        //               "height": 4613,
        //               "width": 3204,
        //               "service": [
        //                 {
        //                   "id": "https://iiif.io/api/image/3.0/example/reference/59d09e6773341f28ea166e9f3c1e674f-gallica_ark_12148_bpt6k1526005v_f18",
        //                   "type": "ImageService3",
        //                   "profile": "level1"
        //                 }
        //               ]
        //             },
        //             "target": "https://iiif.io/api/cookbook/recipe/0009-book-1/canvas/p1"
        //           }
        //         ]
        //       }
        //     ]
        //   }';

        return (object) $canvas;
    }
}
