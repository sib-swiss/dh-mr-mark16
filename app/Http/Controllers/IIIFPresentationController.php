<?php

namespace App\Http\Controllers;

use App\Models\Manuscript;
use Illuminate\Http\JsonResponse;

class IIIFPresentationController extends Controller
{
    /**
     * https://iiif.io/api/presentation/3.0/#51-collection
     */
    public function collection(): JsonResponse
    {
        return response()->json([], 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * https://iiif.io/api/presentation/3.0/#52-manifest
     *
     * Manuscrio V2.1 Ex.: https://mr-mark16.sib.swiss/api/iiif/2-1/GA05/manifest
     * ex. https://iiif.io/api/cookbook/recipe/0009-book-1/manifest.json
     */
    public function manifest(string $manuscriptName): JsonResponse
    {
        $manuscript = Manuscript::firstWhere('name', $manuscriptName);

        return response()->json($manuscript->manifest, 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * https://iiif.io/api/presentation/3.0/#53-canvas
     */
    public function canvas(): JsonResponse
    {
        return response()->json([], 200, [], JSON_PRETTY_PRINT);
    }

    // https://iiif.io/api/presentation/3.0/#54-range
    // https://iiif.io/api/presentation/3.0/#55-annotation-page
    // https://iiif.io/api/presentation/3.0/#57-content-resources
    // https://iiif.io/api/presentation/3.0/#58-annotation-collection
}
