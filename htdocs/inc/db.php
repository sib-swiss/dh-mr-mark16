<?php
// Call data class
require_once $f3->get('MR_PATH') . '/classes/data.php';

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

// Display loaded database content as HTML comment when debug mode enabled
if ($app_config->debug === true) {
    echo '<!-- Loaded database structure: -->' . PHP_EOL;
    echo '<!-- ' . print_r($db, true) . ' -->' . PHP_EOL;
}