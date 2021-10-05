<?php
/**
 * IIIF Presentation API v2 - PHP Implementation
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

// Debug
// echo '<pre>' . print_r($params, true) . '</pre>' . PHP_EOL;
// echo '<pre>' . $f3->get('ROOT') . '</pre>' . PHP_EOL;
// echo '<pre>' . $f3->get('BASE') . '</pre>' . PHP_EOL;
// echo '<pre>' . $f3->get('PATTERN') . '</pre>' . PHP_EOL;
// echo '<pre>' . $f3->get('PATH') . '</pre>' . PHP_EOL;
// if ($f3->app_config->debug === true) {
//     echo '<pre>' . print_r($f3, true) . '</pre>' . PHP_EOL;
// }

// Collect list info
$manuscript_id = $params['id'];
$manuscript_page = str_replace('p', '', $params['page']);

// Allowed file extensions
$valid_extensions = ['png', 'gif', 'jpg', 'jpeg'];

// Return sequence based on sample:
// https://iiif.io/api/presentation/2.1/#sequence

// Build API response
$api_response               = new stdClass();
$api_response->{'@context'} = 'http://iiif.io/api/presentation/2/context.json';
$api_response->{'@id'}      = $f3->get('REALM');
$api_response->{'@type'}    = 'sc:Sequence';
$api_response->label        = 'Current Page Order';
$api_response->viewingHint  = 'paged';

// Create canvases array
$api_response->canvases     = [];

// Iterate over imported manuscripts
// foreach ($f3->app_manuscripts as $manuscript) {
foreach ($f3->app_instance->get_manuscripts() as $manuscript) {
    // Lookup for requested manuscript
    if ($manuscript['name'] === $manuscript_id) {
        // Debug manuscript
        // echo '<pre>' . print_r($manuscript, true) . '</pre>' . PHP_EOL;
        // exit;

        // Get manuscript data
        $manuscript_data = $f3->app_instance->get_manuscript($manuscript['name']);

        // Get manuscript dcterms (main folio only)
        if (count($manuscript['sub_folder']) > 0) {
            $dcterms = $manuscript_data[$manuscript['name']][0]['data']['dcterms'];
        }
        else {
            $dcterms = $manuscript_data[$manuscript['name']]['data']['dcterms'];
        }

        // Iterate over manuscript dcterms
        foreach ($dcterms as $key => $value) {
            switch ($key) {
                case 'dcterm-language':
                    // Add presentation details
                    switch ($dcterms['dcterm-language'][0]) {
                        case 'Arabic':
                            $api_response->viewingDirection = 'right-to-left';
                            break;
                        
                        default:
                            $api_response->viewingDirection = 'left-to-right';
                            break;
                    }
                break;
            }
        }

        // Detect manuscript subfolders
        if (count($manuscript['sub_folder']) > 0) {
            // Init page counter
            $page = 0;

            // Add start canvas to the API response
            $api_response->startCanvas  = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
            $api_response->startCanvas .= 'api/iiif/2/' . $manuscript['name'] . '/canvas/p2';

            // Iterate over manuscript sub folders
            foreach ($manuscript['sub_folder'] as $sub_folder) {
                // Increment page counter
                $page++;

                // Current page
                // echo '<pre>' . PHP_EOL;
                // echo '- Current page: ' . $page . ' / ' . $sub_folder . PHP_EOL;
                // echo '- Requested page: ' . $manuscript_page . PHP_EOL;
                // echo '</pre>' . PHP_EOL;

                // Create minimal canvas object
                $canvas = new stdClass();
                $canvas->{'@context'} = 'http://iiif.io/api/presentation/2/context.json';
                $canvas->{'@id'}  = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                $canvas->{'@id'} .= 'api/iiif/2/' . $manuscript['name'] . '/canvas/p' . $page;
                $canvas->{'@type'} = 'sc:Canvas';
                $canvas->{'label'} = $sub_folder;

                // Add minimal canvas object to the canvases array
                $api_response->canvases[] = $canvas;
            }
        }
        else {
            // Add start canvas to the API response
            $api_response->startCanvas  = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
            $api_response->startCanvas .= 'api/iiif/2/' . $manuscript['name'] . '/canvas/p1';

            // Create minimal canvas object
            $canvas = new stdClass();
            $canvas->{'@context'} = 'http://iiif.io/api/presentation/2/context.json';
            $canvas->{'@id'}  = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
            $canvas->{'@id'} .= 'api/iiif/2/' . $manuscript['name'] . '/canvas/p1';
            $canvas->{'@type'} = 'sc:Canvas';
            $canvas->{'label'} = $manuscript['name'];

            // Add minimal canvas object to the canvases array
            $api_response->canvases[] = $canvas;
        }
    }
}

// Set response as JSON
header('Content-Type: text/json');

// Display IIIF Image API Schema
echo json_encode($api_response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);