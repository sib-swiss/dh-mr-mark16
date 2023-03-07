<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class IIIFImageController extends Controller
{
    /**
     * https://iiif.io/api/image/3.0/#51-image-information-request
     */
    public function requests(Request $request)
    {
        $parameters = $request->route()->parameters();

        // $file = storage_path('public/'.str_replace("__","/",$parameters['identifier']);
        $file = storage_path('app/public/'.str_replace('__', '/', $parameters['identifier']));
        // dd($file);
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
