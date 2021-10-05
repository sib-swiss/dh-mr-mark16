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

// Return collection based on sample:
// https://iiif.io/api/presentation/2.1/#collection

// Build API response
$api_response               = new stdClass();
$api_response->{'@context'} = 'http://iiif.io/api/presentation/2/context.json';
$api_response->{'@id'}      = $f3->get('REALM');
$api_response->{'@type'}    = 'sc:Collection';
$api_response->label        = 'Collection for the Mark16 project';
$api_response->viewingHint  = $params['name'];
$api_response->description  = 'This is the IIIF collection for the Mark16 project';
$api_response->attribution  = 'Provided by SIB / DH+ Group';

// Build collection manifests
$api_response->manifests = [];

// Iterate over imported manuscripts
// foreach ($f3->app_manuscripts as $manuscript) {
foreach ($f3->app_instance->get_manuscripts() as $manuscript) {
    // Generate item object
    $manifest            = new stdClass();
    $manifest->{'@id'}   = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
    $manifest->{'@id'}  .= 'api/iiif/2/' . $manuscript['name'] . '/manifest';
    $manifest->{'@type'} = 'sc:Manifest';

    // Get manuscript data
    $manuscript_data = $f3->app_instance->get_manuscript($manuscript['name']);

    // Get manuscript dcterms (main folio only)
    if (count($manuscript['sub_folder']) > 0) {
        $dcterms = $manuscript_data[$manuscript['name']][0]['data']['dcterms'];
    }
    else {
        $dcterms = $manuscript_data[$manuscript['name']]['data']['dcterms'];
    }

    // in case of folder with only images
    // for manuscript imported trough nakala url
    // we will do not have dcterms using app_instance->get_manuscript
    if ($dcterms) {
        // Iterate over manuscript dcterms
        foreach ($dcterms as $key => $value) {
            switch ($key) {
                case 'dcterm-bibliographicCitation':

                    // Set label from XML data
                    $manifest->label = (string)$dcterms['dcterm-bibliographicCitation'][0];
                    break;
            }
        }
    }
    

    // Import manifest object to collection
    $api_response->manifests[] = $manifest;
}

// Set response as JSON
header('Content-Type: text/json');

// Display IIIF Image API Schema
echo json_encode($api_response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);