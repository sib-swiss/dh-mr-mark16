<?php

/**
 * The first thing we will do is create a new Fat Free application instance
 */

use classes\Db\CreateDb;
use classes\Db\ManuscriptContentFilesystemSeeder;
use classes\Db\ManuscriptContentNakalaSeeder;
use classes\Models\Manuscript;
use classes\Models\User;

// otherwise error while displaying base64_image_content
ini_set('memory_limit', '1024M');

// Init framework
$f3 = \Base::instance();
require __DIR__ . '/config.php';
require __DIR__ . '/framework.php';

// Define DB path
// $dbFullPath = realpath($f3->get('ROOT') . (empty($f3->get('BASE')) ? '/..' : '') . '/data').'/database.sqlite';
// $dbFullPath = $f3->get('ROOT') . (empty($f3->get('BASE')) ? '/..' : '') . '/data/database.sqlite';
$dbFullPath = $f3->get('MR_DATA_DIR') . '/database.sqlite';
// @unlink($dbFullPath);
if (!file_exists($dbFullPath)) {
    $createDb = true;
}

// Set database path in F3
$f3->set('DB', new DB\SQL('sqlite:' . $dbFullPath));

// Feed database if empty
if (isset($createDb) && $createDb === true) {
    // TODO: Create and display loading page while database is re-created

    // Create database
    (new CreateDb())->handle();

    // Init database feeder
    (new ManuscriptContentFilesystemSeeder())->handle();

    // Read stored Nakala URLs
    // $raw_nakala_urls = file_get_contents($f3->get('ROOT') . (empty($f3->get('BASE')) ? '/..' : '') . '/data/nakala.json');
    $raw_nakala_urls = file_get_contents($f3->get('MR_DATA_DIR') . '/nakala.json');
    $nakala_urls = json_decode($raw_nakala_urls)->urls;

    // Load stored Nakala URLs in database
    foreach ($nakala_urls as $url) {
        (new ManuscriptContentNakalaSeeder($url))->handle();
    }
}

//  check database manuscripts table has temporal field
//  we can remove this block when field will be  createdd,
//   or commented out cause can be usefull for adding more fields
$db = $f3->get('DB');
$tableFields = $db->exec('PRAGMA table_info(manuscripts);');
$fieldExists = false;
foreach ($tableFields as $field) {
    if ($field['name'] == 'temporal') {
        $fieldExists = true;
        break;
    }
}
if (!$fieldExists) {
    $db->exec('ALTER TABLE manuscripts ADD temporal INTEGER;');
}
$manuscriptsWithoutTemporal = (new Manuscript)->find(['temporal IS NULL']);
foreach ($manuscriptsWithoutTemporal as $manuscript) {
    $manuscript->temporal = $manuscript->getMeta('dcterm-temporal');
    $manuscript->save();
}


/**
 * function to check Auth (to protect Admin route/resources)
 */
$f3->set('authcheck', function () {
    // First check if a username was provided.
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        // If no username provided, present the auth challenge.
        header('WWW-Authenticate: Basic realm="MR16-admin"');
        header('HTTP/1.0 401 Unauthorized');
        // User will be presented with the username/password prompt
        // If they hit cancel, they will see this access denied message.
        echo '<p>Access denied. You did not enter a password.</p>';
        exit; // Be safe and ensure no other content is returned.
    }
    $user=User::findBy('username', $_SERVER['PHP_AUTH_USER']);
    // If we get here, username was provided. Check password.
    if (!$user || !password_verify($_SERVER['PHP_AUTH_PW'], $user->password)) {
        die('<p>Access denied!</p>');
    }
});

return $f3;
