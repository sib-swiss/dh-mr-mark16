<?php
// Call bootstrap code
require __DIR__ . '/../../../inc/bootstrap.php';

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
$db = $app->get_db();

// Manuscript ID
$manuscript_id = (isset($_GET['id']) ? htmlentities(strip_tags(base64_decode($_GET['id']))) : null);

// Dirty manuscript ID fix
// It will rewrite the decoded manuscript ID to UPPERCASE
// TODO: Fix it better
$manuscript_id = strtoupper($manuscript_id);

// Manuscript Folio ID
$manuscript_folio_id = (isset($_GET['folio']) ? htmlentities(strip_tags(base64_decode($_GET['folio']))) : null);

// API output
$api = [];

// Iterate over existing manuscripts stored in the database
foreach ($db['manuscripts'] as $manuscript) {

    // Search for corresponding manuscript
    if (!is_null($manuscript_id) && $manuscript['name'] === $manuscript_id) {
        $manuscript_name = $manuscript['name'];
        $manuscript_files = $manuscript['content'];
        $manuscript_data = $app->get_manuscript($manuscript_name);
        $manuscript_folios = $app->get_folio_names($manuscript);

        // Store results to API output
        // $api['data'] = $manuscript_data;
        $api['files'] = $manuscript_files;
        $api['folios'] = $manuscript_folios;
    }
}

// Generate API response
if (count($api) > 0) {
    $api['status'] = 'OK'; // Add API response status
    $api['statusCode'] = 200; // Add API response status code
}
else {
    $api['status'] = 'Error'; // Add API response status
    $api['statusCode'] = 404; // Add API response status code
}

// Display API response
header('Content-Type: text/json');
echo json_encode((object)$api, JSON_NUMERIC_CHECK);
?>