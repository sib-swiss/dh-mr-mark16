<?php

namespace iiif21;

use classes\BaseResource;
use classes\Models\Manuscript;
use Log;
use Nakala;
use stdClass;

/**
 * https://iiif.io/api/presentation/2.1/
 */
class PresentationApi extends BaseResource
{
    private $params;
    private $apiUrl;

    public function __construct($params)
    {
        parent::__construct();

        $this->logger = new Log('data/logs/presentationApi.log');
        $this->params = $params;

        $this->apiUrl = $this->f3->get('SCHEME') . '://' . $this->f3->get('SERVER.HTTP_HOST') . $this->f3->get('MR_PATH_WEB')
            . 'api/iiif/2-1/';
    }

    /**
     * Return collection based on sample:
     * https://iiif.io/api/presentation/2.1/#collection

     *
     * @return void
     */
    public function collection()
    {
        // Build API response

        $api_response = new stdClass();
        $api_response->{'@context'} = 'http://iiif.io/api/presentation/2/context.json';
        $api_response->{'@id'} = $this->f3->get('REALM');
        $api_response->{'@type'} = 'sc:Collection';
        $api_response->label = 'Collection for the Mark16 project';
        $api_response->viewingHint = $this->params['name'];
        $api_response->description = 'This is the IIIF collection for the Mark16 project';
        $api_response->attribution = 'Provided by SIB / DH+ Group';

        // Build collection manifests
        $api_response->manifests = [];
        $manuscripts = $this->f3->get('GET.manuscriptID')
            ? Manuscript::where('name', base64_decode($this->f3->get('GET.manuscriptID')))
            : Manuscript::all(['order' => 'temporal ASC']);
        foreach ($manuscripts as $manuscript) {
            $api_response->manifests[] = $manuscript->getManifest('2-1');
        }

        return $this->returnResponse($api_response);
    }

    /**
     * https://iiif.io/api/presentation/2.1/#canvas
     *
     * @return void
     */
    public function canvas()
    {
        $manuscript = Manuscript::findBy('name', $this->params['id']);
        $api_response = new stdClass();
        $api_response->{'@context'} = 'http://iiif.io/api/presentation/2/context.json';
        $api_response->{'@id'} = $this->f3->get('REALM');
        $api_response->{'@type'} = 'sc:Canvas';
        foreach ($manuscript->contentsImage() as $page => $contentImage) {
            $manuscript_image_size = getimagesize($contentImage->getImagePath());
            $manuscript_image_info = pathinfo($contentImage->getImagePath());
            $api_response->label = $manuscript->name;
            $api_response->width = $manuscript_image_size[0];
            $api_response->height = $manuscript_image_size[1];
            $api_response->thumbnail = new stdClass();
            $api_response->thumbnail->{'@id'} = $this->apiUrl . $manuscript->name . 'canvas/p' . $page . '/thumb.jpg';
            $api_response->thumbnail->{'@type'} = 'dctypes:Image';
            $api_response->thumbnail->width = 150;
            $api_response->thumbnail->height = 200;

            // Create canvas images array
            $api_response->images = [];

            // Create image object
            $image = new stdClass();
            $image->{'@type'} = 'oa:Annotation';
            $image->motivation = 'sc:painting';
            $image->resource = new stdClass();
            $image->resource->{'@id'} = $this->apiUrl . $manuscript->name . '/res/page1.' . $manuscript_image_info['extension'];
            $image->resource->{'@type'} = 'dctypes:Image';
            $image->resource->format = 'image/jpeg';
            $image->resource->service = new stdClass();
            $image->resource->service->{'@context'} = 'http://iiif.io/api/image/2/context.json';
            $image->resource->service->{'@id'} = $this->apiUrl . $manuscript->name . '-page' . $page;
            $image->resource->service->profile = 'http://iiif.io/api/image/2/level1.json';
            $image->resource->width = $manuscript_image_size[0];
            $image->resource->height = $manuscript_image_size[1];
            $image->on = $this->f3->get('SCHEME') . '://' . $this->f3->get('SERVER.HTTP_HOST') . $this->f3->get('MR_PATH_WEB');
            $image->on .= 'api/iiif/2-1/' . $manuscript->name . '/canvas/p' . $page;
            ;

            // Add image object to canvas images array
            $api_response->images[] = $image;

            // Create canvas otherContent array
            $api_response->otherContent = [];

            // Create otherContent object
            $otherContent = new stdClass();
            $otherContent->{'@id'} = $this->apiUrl . $manuscript->name . '/list/p' . $page . '.' . strtolower($manuscript_image_info['extension']);
            $otherContent->{'@type'} = 'sc:AnnotationList';
            $otherContent->within = new stdClass();
            $otherContent->within->{'@id'} = $this->apiUrl . $manuscript->name . '/layer/l' . $page;
            $otherContent->within->{'@type'} = 'sc:Layer';
            $otherContent->within->label = 'Example Layer';

            // Add otherContent object to canvas images array
            $api_response->otherContent[] = $otherContent;
        }
        return $this->returnResponse($api_response);
    }

    // Return manifest based on sample:
    // https://iiif.io/api/presentation/2.1/#c-example-manifest-response
    public function manifest()
    {
        // Build API response
        $api_response = new stdClass();
        $api_response->{'@context'} = 'http://iiif.io/api/presentation/2/context.json';
        $api_response->{'@type'} = 'sc:Manifest';
        $api_response->{'@id'} = $this->f3->get('REALM');
        $api_response->metadata = [];

        $manuscript = Manuscript::findBy('name', $this->params['id']);
        // new json nakala
        //dd($dcterms);
        $api_response->label = $manuscript->getDisplayname();

        $meta = new stdClass();
        $meta->label = 'Author';
        $meta->value = $manuscript->getMeta('dcterm-creator');
        $api_response->metadata[] = $meta;

        // Create new value object
        $meta_value = new stdClass();
        $meta_value->{'@value'} = $manuscript->getMeta('dcterm-provenance');
        $meta_value->{'@language'} = $manuscript->getMeta('dcterm-language');
        $meta = new stdClass();
        $meta->label = 'Published';
        $meta->value[] = $meta_value;

        // Add built metas
        $api_response->metadata[] = $meta;

        // Add description
        $api_response->description = $manuscript->getMeta('dcterm-abstract');

        // Add navData
        $api_response->navDate = $manuscript->getMeta('dcterm-created');

        // Add license
        $api_response->license = $manuscript->getMeta('dcterm-license');

        // Add attribution
        $api_response->attribution = 'Provided by ' . $manuscript->getMeta('dcterm-provenance');

        // Add service
        $api_response->service = new stdClass();
        $api_response->service->{'@context'} = $this->apiUrl . 'ns/jsonld/context/json';
        $api_response->service->{'@id'} = $this->apiUrl . 'service/example';
        $api_response->service->profile = $this->apiUrl . 'docs/example-service.html';

        // TODO: Add 'rendering' block
        // TODO: Add 'within' property

        // List of Canvases (where are linked the images)
        $api_response->sequences = [];

        // Build sequence object
        $sequence = new stdClass();
        $sequence->{'@id'} = $this->apiUrl . $manuscript->name . '/sequence/normal';
        $sequence->{'@type'} = 'sc:Sequence';
        $sequence->label = 'Normal Sequence';

        // Add presentation details
        $sequence->viewingHint = 'paged';
        switch ($manuscript->getMeta('dcterm-language')) {
            case 'Arabic':
                $sequence->viewingDirection = 'right-to-left';
                break;

            default:
                $sequence->viewingDirection = 'left-to-right';
                break;
        }

        // Create sequence canvases array
        $sequence->canvases = [];

        foreach ($manuscript->contentsFolios() as $subFolderIndex => $contentFolio) {
            $canvas = $this->manifestImage($contentFolio, $subFolderIndex);
            if ($canvas) {
                $sequence->canvases[] = $canvas;
            }
        }

        $api_response->sequences[] = $sequence;

        // List of structures
        $api_response->structures = [];

        // Create structure object
        $structure = new stdClass();
        $structure->{'@id'} = $this->apiUrl . $manuscript->name . '/range/r1';
        $structure->{'@type'} = 'sc:Range';
        $structure->label = 'Introduction';

        // Create canvases structure array
        $structure->canvases = [];
        foreach ($manuscript->contentsImage() as $loop => $contentImage) {
            $loop++;
            $structure->canvases[] = $this->apiUrl . $manuscript->name . '/canvas/p' . $loop;
        }
        /*
        if (count($manuscript->getSubFolder()) > 0) {
            $loop = 0;
            foreach ($manuscript->getSubFolder() as $folio_canvas) {
                // Increment loop counter
                $loop++;

                // Add canvas to canvases structure array
                $structure->canvases[] = $this->apiUrl . $manuscript_name . '/canvas/p' . $loop;
            }
        } else {
            // Add canvas to canvases structure array
            $structure->canvases[] = $this->apiUrl . $manuscript_name . '/canvas/p1';
        }
        */

        // Add structure object structures array
        $api_response->structures[] = $structure;

        return $this->returnResponse($api_response);
    }

    private function manifestImage($contentFolio, $subFolderIndex)
    {
        $subFolderIndex++;

        $manuscriptName = $contentFolio->manuscript()->name;

        $contentImage = $contentFolio->getFolioImage();
        if (!$contentImage) {
            return;
        }
        // Get image dimensions

        $extension = null;
        $width = null;
        $height = null;

        $details = $contentImage->details();
        $extension = $details['info']['extension'];
        $width = $details['size'][0];
        $height = $details['size'][1];

        // Create canva object
        $canvas = new stdClass();
        $canvas->{'@id'} = $this->apiUrl . $manuscriptName . '/canvas/p' . $subFolderIndex;
        $canvas->{'@type'} = 'sc:Canvas';

        $canvas->label = $contentFolio->getFolioName();

        // Set image dimensions to canvas object
        $canvas->width = $width;
        $canvas->height = $height;

        // Create canvas images array
        $canvas->images = [];

        // Create image object
        $image = new stdClass();
        $image->{'@type'} = 'oa:Annotation';
        $image->motivation = 'sc:painting';
        $image->resource = new stdClass();
        $image->resource->{'@id'} = $this->apiUrl . $manuscriptName . '/res/page' . $subFolderIndex . '.' . $extension;
        $image->resource->{'@type'} = 'dctypes:Image';
        $image->resource->format = 'image/jpeg';
        $image->resource->service = new stdClass();
        $image->resource->service->{'@context'} = 'http://iiif.io/api/image/2/context.json';
        $image->resource->service->{'@id'} = $this->apiUrl . 'images/' . $manuscriptName . '-page' . $subFolderIndex;
        $image->resource->service->profile = 'http://iiif.io/api/image/2/level1.json';
        $image->resource->width = $width;
        $image->resource->height = $height;
        $image->on = $this->apiUrl . $manuscriptName . '/canvas/p' . $subFolderIndex;

        // Add image object to canvas images array
        $canvas->images[] = $image;

        // Create canvas otherContent array
        $canvas->otherContent = [];

        // Create otherContent object
        $otherContent = new stdClass();
        $otherContent->{'@id'} = $this->apiUrl . $manuscriptName . '/list/p' . $subFolderIndex . '.' . strtolower($extension);
        $otherContent->{'@type'} = 'sc:AnnotationList';
        $otherContent->within = new stdClass();
        $otherContent->within->{'@id'} = $this->apiUrl . $manuscriptName . '/layer/l' . $subFolderIndex;
        $otherContent->within->{'@type'} = 'sc:Layer';
        $otherContent->within->label = 'Example Layer';

        // Add otherContent object to canvas images array
        $canvas->otherContent[] = $otherContent;

        return $canvas;
    }

    public function annotationList()
    {
        // Return list based on sample:
        // https://iiif.io/api/presentation/2.1/#annotation-list

        // Build API response
        $api_response = new stdClass();
        $api_response->{'@context'} = 'http://iiif.io/api/presentation/2/context.json';
        $api_response->{'@id'} = $this->f3->get('REALM');
        $api_response->{'@type'} = 'sc:AnnotationList';

        // Create resources array
        $api_response->resources = [];

        return $this->returnResponse($api_response);
    }
}
