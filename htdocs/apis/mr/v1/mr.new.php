<?php
/**
 * Custom MR API
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

// var_dump($data, $app); exit;

// Create database instance
// $db = $app->get_db();

// Import manuscript database to F3
// // $f3->set('app_manuscripts', $db['manuscripts']);

// Import app instance to F3
$f3->set('app_instance', $app);

// Manuscript ID
$manuscript_id = (isset($_GET['id']) ? htmlentities(strip_tags(base64_decode($_GET['id']))) : null);

// Dirty manuscript ID fix
// It will rewrite the decoded manuscript ID to UPPERCASE
// TODO: Fix it better
$manuscript_id = strtoupper($manuscript_id);

// Manuscript Folio ID
$manuscript_folio_id = (isset($_GET['folio']) ? htmlentities(strip_tags(base64_decode($_GET['folio']))) : null);

// Prepare API response
$api_response = [];

// Iterate over existing manuscripts stored in the database
foreach ($f3->app_instance->get_manuscripts() as $manuscript) {

    // Search for corresponding manuscript
    if (!is_null($manuscript_id) && $manuscript['name'] === $manuscript_id) {
        $manuscript_name = $manuscript['name'];
        $manuscript_files = $manuscript['content'];
        $manuscript_data = $f3->app_instance->get_manuscript($manuscript_name);
        $manuscript_folios = $f3->app_instance->get_folio_names($manuscript);

        // Store results to API output
        // $api_response['data'] = $manuscript_data;
        $api_response['files'] = $manuscript_files;
        $api_response['folios'] = $manuscript_folios;
    }
}

// Generate API response
if (count($api_response) > 0) {
    $api_response['status'] = 'OK'; // Add API response status
    $api_response['statusCode'] = 200; // Add API response status code

    // Set response as JSON
    header('Content-Type: text/json');

    // Display API features
    echo json_encode((object)$api_response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}
else {
    /* $api_response['status'] = 'Error'; // Add API response status
    $api_response['statusCode'] = 400; // Add API response status code */

    $f3->error(400);
}
?>