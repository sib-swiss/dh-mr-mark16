<?php
/**
 * IIIF Spec v2 PHP Implementation
 *
 * @author Jonathan BARDA / SIB - 2020
 */

// Fix timeout issues
set_time_limit(60);
ini_set('max_execution_time', 60);

// Call Router
// require __DIR__ . '/../../../router.php';

// Call F3
// if (!isset($f3)) {
//     $f3 = require(__DIR__ . '/../../../libs/fatfree-3.7.2/lib/base.php');
// }

// Call bootstrap code
require __DIR__ . '/../../../inc/bootstrap.php';

// Define include path
// $f3->set('MR_DATA_PATH',
//     (empty($f3->get('BASE'))
//         ? $f3->get('ROOT') . '/../data/manuscripts'
//         : $f3->get('ROOT') . '/data/manuscripts'
//     )
// );

// Import JSON config to F3
// $f3->set('app_config', $app_config);

// Call data class
require_once __DIR__ . '/../../../classes/data.php';

// Init data class
// Most of the class methods will reset the internal counter, maybe that's not good...
$data = new Data();

// Define folder to parse from data/
$data->initialize('manuscripts');

// Load content to the main app
$app->set_content($data->get_structure());

// Create database from loaded content
// $app->create_db('manuscripts');

// Load existing database
$app->load_db('manuscripts');

// Create database instance
// $db = $app->get_db();

// Import manuscript database to F3
// $f3->set('app_manuscripts', $db['manuscripts']);

// Import app instance to F3
$f3->set('app_instance', $app);

// Parse given id
$manuscript_id = $params['id'];

// Allowed file extensions
$valid_extensions = ['gif', 'png', 'jpg', 'jpeg'];

// Return manifest based on sample:
// https://iiif.io/api/presentation/2.1/#c-example-manifest-response

// Build API response
$api_response               = new stdClass();
$api_response->{'@context'} = 'http://iiif.io/api/presentation/2/context.json';
$api_response->{'@type'}    = 'sc:Manifest';
$api_response->{'@id'}      = $f3->get('REALM');
$api_response->metadata     = [];

// Iterate over imported manuscripts
// foreach ($f3->app_manuscripts as $manuscript) {
foreach ($f3->app_instance->get_manuscripts() as $manuscript) {
    if ($manuscript['name'] === $manuscript_id) {
        // Set manuscript name
        $manuscript_name = $manuscript['name'];

        // Get manuscript data
        $manuscript_data = $f3->app_instance->get_manuscript($manuscript['name']);

        // Get manuscript dcterms (main folio only)
        if (count($manuscript['sub_folder']) > 0) {
            $dcterms = $manuscript_data[$manuscript['name']][0]['data']['dcterms'];
        }
        else {
            $dcterms = $manuscript_data[$manuscript['name']]['data']['dcterms'];
        }

        // Set label from XML data
        $api_response->label = (string)$dcterms['dcterm-bibliographicCitation'][0];

        // Iterate over manuscript dcterms
        foreach ($dcterms as $key => $value) {
            switch ($key) {
                case 'dcterm-creator':
                    $meta              = new stdClass();
                    $meta->label       = 'Author';
                    $meta->value       = (string)$value[0];

                    // Add built metas
                    $api_response->metadata[] = $meta;
                    break;

                case 'dcterm-provenance':
                    $meta            = new stdClass();
                    $meta->label     = 'Published';
                    $meta->value     = [];

                    // Create new value object
                    $meta_value = new stdClass();
                    $meta_value->{'@value'} = (string)$value[0];
                    $meta_value->{'@language'} = (string)$dcterms['dcterm-language'][0];
                    $meta->value[] = $meta_value;

                    // Add built metas
                    $api_response->metadata[] = $meta;
                    break;
            }
        }

        // Add description
        $api_response->description = (string)$dcterms['dcterm-abstract'][0];

        // Add navData
        $api_response->navDate = (string)$dcterms['dcterm-created'][0];

        // Add license
        $api_response->license = (string)$dcterms['dcterm-license'][0];

        // Add attribution
        $api_response->attribution = 'Provided by ' . (string)$dcterms['dcterm-provenance'][0];

        // Add service
        $api_response->service = new stdClass();
        $api_response->service->{'@context'}  = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
        $api_response->service->{'@context'} .= 'api/iiif/2/ns/jsonld/context/json';
        $api_response->service->{'@id'}  = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
        $api_response->service->{'@id'} .= 'api/iiif/2/service/example';
        $api_response->service->profile = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
        $api_response->service->profile .= 'api/iiif/2/docs/example-service.html';

        // TODO: Add 'rendering' block
        // TODO: Add 'within' property

        // List of Canvases (where are linked the images)
        $api_response->sequences = [];

        // Build sequence object
        $sequence            = new stdClass();
        $sequence->{'@id'}   = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
        $sequence->{'@id'}  .= 'api/iiif/2/' . $manuscript_name . '/sequence/normal';
        $sequence->{'@type'} = 'sc:Sequence';
        $sequence->label     = 'Normal Sequence';

        // Add presentation details
        $sequence->viewingHint = 'paged';
        switch ($dcterms['dcterm-language'][0]) {
            case 'Arabic':
                $sequence->viewingDirection = 'right-to-left';
                break;
            
            default:
                $sequence->viewingDirection = 'left-to-right';
                break;
        }

        // Create sequence canvases array
        $sequence->canvases = [];

        // Gather all sub folders
        $sub_folder_index = 0;
        if (count($manuscript['sub_folder']) > 0) {
            // Iterate over all discovered folios
            foreach ($manuscript['sub_folder'] as $folio_name) {
                // Increment sub folder index
                $sub_folder_index++;

                // Gather image info
                foreach ($manuscript['content'] as $file) {
                    // Stop at found image
                    if (stripos($file, $folio_name) !== false) {
                        // Define manuscript path
                        // $manuscript_path       = __DIR__ . '/../../../../data/manuscripts/' . $manuscript_name;
                        $manuscript_path = $f3->get('MR_DATA_PATH') . '/' . $manuscript['name'];
                        $manuscript_image      = $file;
                        $manuscript_image_path = $manuscript_path . '/' . $file;

                        // Detect file extension
                        $file_extension = pathinfo($manuscript_image_path . '/' . $file, PATHINFO_EXTENSION);

                        // Validate file extension
                        if (in_array($file_extension, $valid_extensions) && stripos($file, 'partner') === false) {
                        // if (stripos($file, '.jpg') !== false || stripos($file, '.jpeg') !== false) {

                            // Get image dimensions
                            $image_size = getimagesize($manuscript_image_path);
                            $image_details = pathinfo($manuscript_image_path);

                            // Create canva object
                            $canvas = new stdClass();
                            $canvas->{'@id'}   = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                            $canvas->{'@id'}  .= 'api/iiif/2/' . $manuscript_name . '/canvas/p' . $sub_folder_index;
                            $canvas->{'@type'} = 'sc:Canvas';
                            $canvas->label     = str_replace('_', ' ', $folio_name);

                            // Set image dimensions to canvas object
                            $canvas->width     = $image_size[0];
                            $canvas->height    = $image_size[1];

                            // Create canvas images array
                            $canvas->images    = [];

                            // Create image object
                            $image = new stdClass();
                            $image->{'@type'} = 'oa:Annotation';
                            $image->motivation = 'sc:painting';
                            $image->resource = new stdClass();
                            $image->resource->{'@id'}   = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                            $image->resource->{'@id'}  .= 'api/iiif/2/' . $manuscript_name . '/res/page' . $sub_folder_index . '.' . $image_details['extension'];
                            $image->resource->{'@type'} = 'dctypes:Image';
                            // $image->resource->format = 'image/jpeg';
                            $image->resource->format = $image_size['mime'];
                            $image->resource->service = new stdClass();
                            $image->resource->service->{'@context'} = 'http://iiif.io/api/image/2/context.json';
                            $image->resource->service->{'@id'}  = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                            $image->resource->service->{'@id'} .= 'api/iiif/2/images/' . $manuscript_name . '-page' . $sub_folder_index;
                            $image->resource->service->profile  = 'http://iiif.io/api/image/2/level1.json';
                            $image->resource->width             = $image_size[0];
                            $image->resource->height            = $image_size[1];
                            $image->on                          = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                            $image->on                         .= 'api/iiif/2/' . $manuscript_name . '/canvas/p' . $sub_folder_index;

                            // Add image object to canvas images array
                            $canvas->images[] = $image;

                            // Create canvas otherContent array
                            $canvas->otherContent = [];

                            // Create otherContent object
                            $otherContent                    = new stdClass();
                            $otherContent->{'@id'}           = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                            $otherContent->{'@id'}          .= 'api/iiif/2/' . $manuscript_name . '/list/p' . $sub_folder_index . '.' . strtolower($image_details['extension']);
                            $otherContent->{'@type'}         = 'sc:AnnotationList';
                            $otherContent->within            = new stdClass();
                            $otherContent->within->{'@id'}   = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                            $otherContent->within->{'@id'}  .= 'api/iiif/2/' . $manuscript_name . '/layer/l' . $sub_folder_index;
                            $otherContent->within->{'@type'} = 'sc:Layer';
                            $otherContent->within->label     = 'Example Layer';

                            // Add otherContent object to canvas images array
                            $canvas->otherContent[] = $otherContent;
                        }
                    }
                }

                // Add canvas object to canvases array
                $sequence->canvases[] = $canvas;
            }

            // Add all sequences
            $api_response->sequences[] = $sequence;
        }

        // Read the single one
        else {
            // Build sequence object
            $sequence            = new stdClass();
            $sequence->{'@id'}   = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
            $sequence->{'@id'}  .= 'api/iiif/2/' . $manuscript_name . '/sequence/normal';
            $sequence->{'@type'} = 'sc:Sequence';
            $sequence->label     = 'Normal Sequence';

            // Add presentation details
            $sequence->viewingHint = 'paged';
            switch ($dcterms['dcterm-language'][0]) {
                case 'Arabic':
                    $sequence->viewingDirection = 'right-to-left';
                    break;
                
                default:
                    $sequence->viewingDirection = 'left-to-right';
                    break;
            }

            // Create sequence canvases array
            $sequence->canvases = [];

            // Gather file info
            foreach ($manuscript['content'] as $file) {
                // Define manuscript path
                // $manuscript_path       = __DIR__ . '/../../../../data/manuscripts/' . $manuscript_name;
                $manuscript_path = $f3->get('MR_DATA_PATH') . '/' . $manuscript['name'];
                $manuscript_image      = $file;
                $manuscript_image_path = $manuscript_path . '/' . $file;

                // Detect file extension
                $file_extension = pathinfo($manuscript_image_path . '/' . $file, PATHINFO_EXTENSION);

                // Validate file extension
                if (in_array($file_extension, $valid_extensions) && stripos($file, 'partner') === false) {
                // if (stripos($file, '.jpg') !== false || stripos($file, '.jpeg') !== false) {

                    // Get image dimensions
                    $image_size = getimagesize($manuscript_image_path);
                    $image_details = pathinfo($manuscript_image_path);

                    // Create canva object
                    $canvas = new stdClass();
                    $canvas->{'@id'}   = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                    $canvas->{'@id'}  .= 'api/iiif/2/' . $manuscript_name . '/canvas/p1';
                    $canvas->{'@type'} = 'sc:Canvas';
                    $canvas->label     = str_replace('_', ' ', $manuscript_name);

                    // Set image dimensions to canvas object
                    $canvas->width     = $image_size[0];
                    $canvas->height    = $image_size[1];

                    // Create canvas images array
                    $canvas->images    = [];

                    // Create image object
                    $image = new stdClass();
                    $image->{'@type'} = 'oa:Annotation';
                    $image->motivation = 'sc:painting';
                    $image->resource = new stdClass();
                    $image->resource->{'@id'}   = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                    $image->resource->{'@id'}  .= 'api/iiif/2/' . $manuscript_name . '/res/page1.' . $image_details['extension'];
                    $image->resource->{'@type'} = 'dctypes:Image';
                    // $image->resource->format = 'image/jpeg';
                    $image->resource->format = $image_size['mime'];
                    $image->resource->service = new stdClass();
                    $image->resource->service->{'@context'} = 'http://iiif.io/api/image/2/context.json';
                    $image->resource->service->{'@id'}  = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                    $image->resource->service->{'@id'} .= 'api/iiif/2/images/' . $manuscript_name . '-page1';
                    $image->resource->service->profile  = 'http://iiif.io/api/image/2/level1.json';
                    $image->resource->width             = $image_size[0];
                    $image->resource->height            = $image_size[1];
                    $image->on                          = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                    $image->on                         .= 'api/iiif/2/' . $manuscript_name . '/canvas/p1';

                    // Add image object to canvas images array
                    $canvas->images[] = $image;

                    // Create canvas otherContent array
                    $canvas->otherContent = [];

                    // Create otherContent object
                    $otherContent                    = new stdClass();
                    $otherContent->{'@id'}           = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                    $otherContent->{'@id'}          .= 'api/iiif/2/' . $manuscript_name . '/list/p1.' . strtolower($image_details['extension']);
                    $otherContent->{'@type'}         = 'sc:AnnotationList';
                    $otherContent->within            = new stdClass();
                    $otherContent->within->{'@id'}   = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                    $otherContent->within->{'@id'}  .= 'api/iiif/2/' . $manuscript_name . '/layer/l1';
                    $otherContent->within->{'@type'} = 'sc:Layer';
                    $otherContent->within->label     = 'Example Layer';

                    // Add otherContent object to canvas images array
                    $canvas->otherContent[] = $otherContent;
                }
            }

            // Add canvas object to canvases array
            $sequence->canvases[] = $canvas;

            // Add all sequences
            $api_response->sequences[] = $sequence;
        }

        // List of structures
        $api_response->structures = [];

        // Create structure object
        $structure            = new stdClass();
        $structure->{'@id'}   = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
        $structure->{'@id'}  .= 'api/iiif/2/' . $manuscript_name . '/range/r1';
        $structure->{'@type'} = 'sc:Range';
        $structure->label     = 'Introduction';

        // Create canvases structure array
        $structure->canvases  = [];
        if (count($manuscript['sub_folder']) > 0) {
            $loop = 0;
            foreach ($manuscript['sub_folder'] as $folio_canvas) {
                // Increment loop counter
                $loop++;

                // Add canvas to canvases structure array
                $structure->canvases[] = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB') . 'api/iiif/2/' . $manuscript_name . '/canvas/p' . $loop;
            }
        }
        else {
            // Add canvas to canvases structure array
            $structure->canvases[] = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB') . 'api/iiif/2/' . $manuscript_name . '/canvas/p1';
        }

        // Add structure object structures array
        $api_response->structures[] = $structure;
    }

}

// Set response as JSON
header('Content-Type: text/json');

// Display IIIF Image API Schema
echo json_encode($api_response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);