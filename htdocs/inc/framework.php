<?php
// Call F3
// if (!isset($f3)) {
//     $f3 = require(__DIR__ . '/../libs/fatfree-3.7.2/lib/base.php');
// }

// Define debug level
// https://fatfreeframework.com/3.7/quick-reference#DEBUG
if ($_SERVER['SERVER_NAME'] === 'localhost' || stripos($_SERVER['SERVER_NAME'], '-dev') !== false) {
    $f3->set('DEBUG', 3);
    // $f3->set('DEBUG', 2);
    // $f3->set('DEBUG', 1);
} else {
    $f3->set('DEBUG', 0);
}

// Frame protection
// https://fatfreeframework.com/3.7/quick-reference#XFRAME
// https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options
// $f3->set('XFRAME', null);
$f3->set('XFRAME', 'SAMEORIGIN');

// Define data folder
$f3->set(
    'MR_DATA_DIR',
    (
        empty($f3->get('BASE'))
        ? realpath($f3->get('ROOT') . '/../data')
        : realpath($f3->get('ROOT') . '/data')
    )
);

// Define data path
$f3->set('MR_DATA_PATH', $f3->get('MR_DATA_DIR') . '/manuscripts');

// Define root path
$f3->set(
    'MR_PATH',
    (
        empty($f3->get('BASE'))
        ? $f3->get('ROOT')
        // : $f3->get('ROOT') . '/' . $f3->get('BASE')
        : $f3->get('ROOT') . $f3->get('BASE')
    )
);

// Define web root path
$f3->set('MR_PATH_WEB', (!empty($f3->get('BASE')) ? $f3->get('BASE') . '/' : '/'));

// Patch API routes
$f3->set('MR_IIIF_SEPARATOR', (empty($f3->get('BASE')) ? '.' : '/'));

// Import JSON config to F3
if (isset($app_config)) {
    $f3->set('MR_CONFIG', $app_config);
    
    // Define SHM cache path
    if (!file_exists($f3->get('MR_CONFIG')->cache->path)) {
        mkdir($f3->get('MR_CONFIG')->cache->path, 0777, true);
    }
    if ($f3->get('MR_CONFIG')->maintenance === true || $f3->get('MR_CONFIG')->cache->clear === true) {
        // Changes:
        // - Removed verbosity as it is not required here
        // - It also speed up the deletion process when verbosity is not enabled
        exec('rm -rf ' . $f3->get('MR_CONFIG')->cache->path . '*');
    }

    // Define CACHE path (based on SHM path)
    $f3->set('CACHE', 'folder=' . $f3->get('MR_CONFIG')->cache->path);

    // Define TEMP path (don't forget the leading '/' at the end)
    # doesn't work on osx, osx doesn't have /dev/shm, I changed locally on config.json
    #$f3->set('TEMP', '/dev/shm/');
    $f3->set('TEMP', $f3->get('MR_CONFIG')->cache->path . 'template/');
}

// Define log path
/* $f3->set(
    'LOGS',
    (
        empty($f3->get('BASE'))
        ? $f3->get('ROOT') . '/../data/logs/'
        : $f3->get('ROOT') . '/data/logs/'
    )
); */
$f3->set('LOGS', $f3->get('MR_DATA_DIR') . '/logs/');

// Create log folder if missing
if (!is_dir($f3->get('LOGS'))) {
    mkdir($f3->get('LOGS'));
}

// Define UI path
$f3->set('UI', '.'.(!empty($f3->get('BASE')) ? $f3->get('BASE') . '/' : '/'));
