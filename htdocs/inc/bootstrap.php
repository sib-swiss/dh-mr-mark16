<?php
// Load main config
require 'config.php';

// Load framework
require 'framework.php';

// Load global functions
require_once 'functions.php';

// Load the App class
require_once $f3->get('MR_PATH') . '/classes/app.php';

// Init App
$app = new App();

// Setup app config
$app->set_config($app_config);

// Setup app config
$app->set_data_folder($f3->get('MR_DATA_DIR'));

// Init cache
$app->init_cache();

// Clear cache if maintenance mode is enabled
if ($app_config->maintenance === true || $app_config->cache->clear === true) {
    $app->clear_cache();
}