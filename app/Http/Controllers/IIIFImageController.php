<?php

namespace App\Http\Controllers;

use App\Models\Manuscript;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class IIIFImageController extends Controller
{
    /**
     * https://iiif.io/api/image/3.0/#51-image-information-request
     */
    public function requests(Request $request)
    {
        $parameters = $request->route()->parameters();
        $media = Media::findOrFail($parameters['identifier']);

        $file = ($media->model instanceof Manuscript)
            ? $media->getPath()
            : $media->model->imageWithCopyright();

        $factory = new \Conlect\ImageIIIF\ImageFactory;

        $file = $factory()->load($file)
            ->withParameters($parameters)
            ->stream();

        $response = Response::make($file);

        $response->header('Content-Type', config("iiif.mime.{$parameters['format']}"));

        return $response;
    }

    /**
     * https://iiif.io/api/image/3.0/#51-image-information-request
     */
    public function info(Request $request)
    {
        $file = storage_path('app/public/'.str_replace('__', '/', $request->identifier));
        $factory = new \Conlect\ImageIIIF\ImageFactory;

        $info = $factory()->load($file)
            ->info('iiif', $request->identifier);

        return $info;
    }
}
