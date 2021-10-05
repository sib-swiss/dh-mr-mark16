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
$valid_extensions = ['gif', 'jpg', 'jpeg', 'xml'];

// Return list based on sample:
// https://iiif.io/api/presentation/2.1/#annotation-list

// Build API response
$api_response               = new stdClass();
$api_response->{'@context'} = 'http://iiif.io/api/presentation/2/context.json';
$api_response->{'@id'}      = $f3->get('REALM');
$api_response->{'@type'}    = 'sc:AnnotationList';

// Create resources array
$api_response->resources = [];

// Iterate over imported manuscripts
// foreach ($f3->app_manuscripts as $manuscript) {
foreach ($f3->app_instance->get_manuscripts() as $manuscript) {
    // Lookup for requested manuscript
    if ($manuscript['name'] === $manuscript_id) {
        // Debug manuscript
        // echo '<pre>' . print_r($manuscript, true) . '</pre>' . PHP_EOL;
        // exit;

        // Detect manuscript subfolders
        if (count($manuscript['sub_folder']) > 0) {
            // Init page counter
            $page = 0;

            // Iterate over manuscript sub folders
            foreach ($manuscript['sub_folder'] as $sub_folder) {
                // Increment page counter
                $page++;

                // Current page
                // echo '<pre>' . PHP_EOL;
                // echo '- Current page: ' . $page . ' / ' . $sub_folder . PHP_EOL;
                // echo '- Requested page: ' . $manuscript_page . PHP_EOL;
                // echo '</pre>' . PHP_EOL;

                // Select requested page
                if (isset($manuscript_page) && $manuscript_page === $page) {
                    // echo '<pre>==> Selected: ' . $sub_folder . '</pre>' . PHP_EOL;

                    // Iterate over manuscript content
                    foreach ($manuscript['content'] as $file) {
                        // Stop at found page
                        if (stripos($file, $sub_folder) !== false) {
                            // Set path
                            // $manuscript_path = $f3->get('ROOT') . '/data/manuscripts/' . $manuscript['name'];
                            $manuscript_path = $f3->get('MR_DATA_PATH') . '/' . $manuscript['name'];

                            // Detect file extension
                            $file_extension = pathinfo($manuscript_path . '/' . $file, PATHINFO_EXTENSION);

                            // Current file
                            // echo '<pre>' . PHP_EOL;
                            // echo '- Path: ' . $manuscript_path . PHP_EOL;
                            // echo '- Current file: ' . $file . PHP_EOL;
                            // echo '- Requested format: ' . $params['format'] . PHP_EOL;
                            // echo '- File extension: ' . $file_extension . PHP_EOL;
                            // echo '- File details: ' . print_r(pathinfo($manuscript_path . '/' . $file), true) . PHP_EOL;
                            // echo '</pre>' . PHP_EOL;

                            // Validate file extension
                            if (in_array($file_extension, $valid_extensions)) {

                                // File candidate
                                // echo '<pre>' . PHP_EOL;
                                // echo '==> Path: ' . $manuscript_path . PHP_EOL;
                                // echo '==> File candidate: ' . $file . PHP_EOL;
                                // echo '==> File extension: ' . $file_extension . PHP_EOL;
                                // echo '==> Mime id: ' . exif_imagetype($manuscript_path . '/' . $file) . PHP_EOL;
                                // echo '==> Mime type: ' . mime_content_type($manuscript_path . '/' . $file) . PHP_EOL;
                                // echo '==> Mime type extension: ' . image_type_to_extension(exif_imagetype($manuscript_path . '/' . $file), false) . PHP_EOL;
                                // echo '==> Requested format: ' . $params['format'] . PHP_EOL;
                                // echo '==> File details: ' . print_r(pathinfo($manuscript_path . '/' . $file), true) . PHP_EOL;
                                // echo '</pre>' . PHP_EOL;

                                // Select file
                                $manuscript_file = $manuscript_path . '/' . $file;

                                // Collect image details
                                $file_info = pathinfo($manuscript_file);
                                // $manuscript_mime_id = exif_imagetype($manuscript_file);
                                // $manuscript_mime = image_type_to_mime_type($manuscript_image_mime_id);

                                // Filter resources by extension
                                switch (strtolower($file_info['extension'])) {
                                    case 'jpeg':
                                    case 'jpg':
                                    case 'gif':
                                        // Create resource object
                                        $res = new stdClass();
                                        $res->{'@type'} = 'oa:Annotation';
                                        $res->motivation = 'sc:painting';
                                        $res->resource = new stdClass();
                                        $res->resource->{'@id'}  = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                                        $res->resource->{'@id'} .= 'api/iiif/2/' . $manuscript['name'] . '/res/' . $file_info['basename'];
                                        $res->resource->{'@type'} = 'dctypes:Image';
                                        $res->resource->format = mime_content_type($manuscript_path . '/' . $file);
                                        $res->on  = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                                        $res->on .= 'api/iiif/2/' . $manuscript['name'] . '/canvas/' . $params['page'];

                                        // Add resource object to list resources array
                                        $api_response->resources[] = $res;
                                        break;

                                    case 'xml':
                                        // Create resource object
                                        $res = new stdClass();
                                        $res->{'@type'} = 'oa:Annotation';
                                        $res->motivation = 'sc:painting';
                                        $res->resource = new stdClass();
                                        $res->resource->{'@id'}  = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                                        $res->resource->{'@id'} .= 'api/iiif/2/' . $manuscript['name'] . '/res/' . $file_info['basename'];
                                        $res->resource->{'@type'} = 'dctypes:Text';
                                        $res->resource->format = 'application/tei+xml';
                                        $res->on  = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                                        $res->on .= 'api/iiif/2/' . $manuscript['name'] . '/canvas/' . $params['page'];

                                        // Add resource object to list resources array
                                        $api_response->resources[] = $res;
                                        break;
                                }
                            }
                        }
                    }

                    // Exit the loop
                    break;
                }
            }
        }
        else {

            // Iterate over manuscript content
            foreach ($manuscript['content'] as $file) {
                // Set path
                // $manuscript_path = $f3->get('ROOT') . '/data/manuscripts/' . $manuscript['name'];
                $manuscript_path = $f3->get('MR_DATA_PATH') . '/' . $manuscript['name'];

                // Detect file extension
                $file_extension = pathinfo($manuscript_path . '/' . $file, PATHINFO_EXTENSION);

                // Current file
                // echo '<pre>' . PHP_EOL;
                // echo '- Path: ' . $manuscript_path . PHP_EOL;
                // echo '- Current file: ' . $file . PHP_EOL;
                // echo '- Requested format: ' . $params['format'] . PHP_EOL;
                // echo '- File extension: ' . $file_extension . PHP_EOL;
                // echo '- File details: ' . print_r(pathinfo($manuscript_path . '/' . $file), true) . PHP_EOL;
                // echo '</pre>' . PHP_EOL;

                // Validate file extension
                if (in_array($file_extension, $valid_extensions)) {

                    // File candidate
                    // echo '<pre>' . PHP_EOL;
                    // echo '==> Path: ' . $manuscript_path . PHP_EOL;
                    // echo '==> File candidate: ' . $file . PHP_EOL;
                    // echo '==> File extension: ' . $file_extension . PHP_EOL;
                    // echo '==> Mime id: ' . exif_imagetype($manuscript_path . '/' . $file) . PHP_EOL;
                    // echo '==> Mime type: ' . mime_content_type($manuscript_path . '/' . $file) . PHP_EOL;
                    // echo '==> Mime type extension: ' . image_type_to_extension(exif_imagetype($manuscript_path . '/' . $file), false) . PHP_EOL;
                    // echo '==> Requested format: ' . $params['format'] . PHP_EOL;
                    // echo '==> File details: ' . print_r(pathinfo($manuscript_path . '/' . $file), true) . PHP_EOL;
                    // echo '</pre>' . PHP_EOL;

                    // Select file
                    $manuscript_file = $manuscript_path . '/' . $file;

                    // Collect image details
                    $file_info = pathinfo($manuscript_file);
                    // $manuscript_mime_id = exif_imagetype($manuscript_file);
                    // $manuscript_mime = image_type_to_mime_type($manuscript_image_mime_id);

                    // Filter resources by extension
                    switch (strtolower($file_info['extension'])) {
                        case 'jpeg':
                        case 'jpg':
                        case 'gif':
                            // Create resource object
                            $res = new stdClass();
                            $res->{'@type'} = 'oa:Annotation';
                            $res->motivation = 'sc:painting';
                            $res->resource = new stdClass();
                            $res->resource->{'@id'}  = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                            $res->resource->{'@id'} .= 'api/iiif/2/' . $manuscript['name'] . '/res/' . $file_info['basename'];
                            $res->resource->{'@type'} = 'dctypes:Image';
                            $res->resource->format = mime_content_type($manuscript_path . '/' . $file);
                            $res->on  = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                            $res->on .= 'api/iiif/2/' . $manuscript['name'] . '/canvas/' . $params['page'];

                            // Add resource object to list resources array
                            $api_response->resources[] = $res;
                            break;

                        case 'xml':
                            // Create resource object
                            $res = new stdClass();
                            $res->{'@type'} = 'oa:Annotation';
                            $res->motivation = 'sc:painting';
                            $res->resource = new stdClass();
                            $res->resource->{'@id'}  = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                            $res->resource->{'@id'} .= 'api/iiif/2/' . $manuscript['name'] . '/res/' . $file_info['basename'];
                            $res->resource->{'@type'} = 'dctypes:Text';
                            $res->resource->format = 'application/tei+xml';
                            $res->on  = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                            $res->on .= 'api/iiif/2/' . $manuscript['name'] . '/canvas/' . $params['page'];

                            // Add resource object to list resources array
                            $api_response->resources[] = $res;
                            break;
                    }
                }
            }
        }
    }
}

// Set response as JSON
header('Content-Type: text/json');

// Display IIIF Image API Schema
echo json_encode($api_response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);