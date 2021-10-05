<?php
/**
 * Routing script
 * 
 * Use it that way: php -S localhost:port -t DOCUMENT_ROOT router-script.php
 * 
 * Taken from: https://www.sitepoint.com/taking-advantage-of-phps-built-in-server/
 * And: https://stackoverflow.com/questions/27765753/routing-php-5-4-built-in-web-server-like-htaccess
 * 
 * Modified by: Jonathan BARDA / SIB - 2020
 * 
 * Should be replaced by: https://fatfreeframework.com/3.7/routing-engine#DynamicWebSites
 * 
 * Required for local dev to simulate .htaccess file / mod-rewrite rules
 * 
 */

// Set timezone
// date_default_timezone_set("UTC");

// Directory that contains error pages
// define("ERRORS", dirname(__FILE__) . "/errors");

// Default index file
define("DIRECTORY_INDEX", "index.php");

// Optional array of authorized client IPs for a bit of security
$config["hostsAllowed"] = array();

// Logging function
// Beware that it will break the default line coloring
function logAccess($status = 200) {
    file_put_contents("php://stdout", sprintf("[%s] %s:%s [%s]: %s" . PHP_EOL,
        date("D M j H:i:s Y"), $_SERVER["REMOTE_ADDR"],
        $_SERVER["REMOTE_PORT"], $status, $_SERVER["REQUEST_URI"])
    );
}

// Parse allowed host list
if (!empty($config['hostsAllowed'])) {
    if (!in_array($_SERVER['REMOTE_ADDR'], $config['hostsAllowed'])) {
        logAccess(403);
        http_response_code(403);
        // include ERRORS . '/403.php';
        exit;
    }
}

// if requesting a directory then serve the default index
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$ext = pathinfo($path, PATHINFO_EXTENSION);
if (empty($ext)) {
    $path = rtrim($path, "/") . "/" . DIRECTORY_INDEX;
}

// IIIF API routes
if (stripos($path, 'iiif/') !== false && preg_match('/\.(?:png|jpg|jpeg|gif|json)$/', $path)) {
    // Parse file name
    $file_info = pathinfo($_SERVER['REQUEST_URI']);

    // Redirect to internal route
    header('Location: ' . $file_info['dirname'] . '/' . $file_info['filename'] . '/' . $file_info['extension']);
    exit;
}

// F3 UI route
elseif (stripos($path, 'f3') !== false) {
    echo '<html><head><title>Local routing exception</title></head><body>' . PHP_EOL;
    echo '<p>As there is no real web server involved and F3 is already loaded, this route does not work locally, you have to open a terminal and run the following commands:</p>' . PHP_EOL;
    echo '<pre>' . PHP_EOL;
    echo 'cd ' . __DIR__ . PHP_EOL;
    echo 'php -S localhost:8885 -t libs/fatfree-3.7.2 ' . PHP_EOL;
    echo '</pre>' . PHP_EOL;
    echo '<p>Then navigate to <a href="http://localhost:8885">http://localhost:8885</a></p>' . PHP_EOL;
    echo '</body></html>' . PHP_EOL;
    exit;
}

// Default server response
else {
    // Check if path contains DIRECTORY_INDEX
    if (stripos($path, DIRECTORY_INDEX) !== false) {
        require DIRECTORY_INDEX;
    }

    // Static file
    else {
        // If the file exists then return false and let the server handle it
        if (file_exists($_SERVER["DOCUMENT_ROOT"] . $path)) {
            return false;
        }
    }
}