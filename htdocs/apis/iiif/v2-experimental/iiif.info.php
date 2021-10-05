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

// Parse given id
$parsed_id = explode('-', $params['id']);
$manuscript_id = $parsed_id[0];
$manuscript_page = (int)str_replace('page', '', $parsed_id[1]);

// Allowed file extensions
$valid_extensions = ['png', 'gif', 'jpg', 'jpeg'];

// Return info based on sample:
// https://iiif.io/api/image/2.1/#image-information

// Build API response
$api_response               = new stdClass();
$api_response->{'@context'} = 'http://iiif.io/api/image/2/context.json';

// Iterate over imported manuscripts
// foreach ($f3->app_manuscripts as $manuscript) {
foreach ($f3->app_instance->get_manuscripts() as $manuscript) {
    // Debug manuscript
    // echo '<pre>Currently analyzed manuscript: ' . print_r($manuscript, true) . '</pre>' . PHP_EOL;

    // Lookup for requested manuscript
    if ($manuscript['name'] === $manuscript_id) {
        // Debug manuscript
        // echo '<pre>Found manuscript: ' . print_r($manuscript, true) . '</pre>' . PHP_EOL;

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

                    // Debug selected page
                    // echo '<pre>==> Selected: ' . $sub_folder . '</pre>' . PHP_EOL;

                    // Iterate over manuscript content
                    foreach ($manuscript['content'] as $file) {

                        // Stop at found page
                        if (stripos($file, $sub_folder) !== false) {

                            // Image path
                            // $manuscript_image_path = $f3->get('ROOT') . '/data/manuscripts/' . $manuscript['name'];
                            $manuscript_image_path = $f3->get('MR_DATA_PATH') . '/' . $manuscript['name'];

                            // Detect file extension
                            $file_extension = pathinfo($manuscript_image_path . '/' . $file, PATHINFO_EXTENSION);

                            // Current file
                            // echo '<pre>' . PHP_EOL;
                            // echo '- Path: ' . $manuscript_image_path . PHP_EOL;
                            // echo '- Current file: ' . $file . PHP_EOL;
                            // echo '- Requested format: ' . $params['format'] . PHP_EOL;
                            // echo '- File extension: ' . $file_extension . PHP_EOL;
                            // echo '- File details: ' . print_r(pathinfo($manuscript_image_path . '/' . $file), true) . PHP_EOL;
                            // echo '</pre>' . PHP_EOL;

                            // Validate file extension
                            if (in_array($file_extension, $valid_extensions)) {

                                // File candidate
                                // echo '<pre>' . PHP_EOL;
                                // echo '==> Path: ' . $manuscript_image_path . PHP_EOL;
                                // echo '==> File candidate: ' . $file . PHP_EOL;
                                // echo '==> File extension: ' . $file_extension . PHP_EOL;
                                // echo '==> Mime id: ' . exif_imagetype($manuscript_image_path . '/' . $file) . PHP_EOL;
                                // echo '==> Mime type: ' . mime_content_type($manuscript_image_path . '/' . $file) . PHP_EOL;
                                // echo '==> Mime type extension: ' . image_type_to_extension(exif_imagetype($manuscript_image_path . '/' . $file), false) . PHP_EOL;
                                // echo '==> Requested format: ' . $params['format'] . PHP_EOL;
                                // echo '==> File details: ' . print_r(pathinfo($manuscript_image_path . '/' . $file), true) . PHP_EOL;
                                // echo '</pre>' . PHP_EOL;

                                // Select file
                                $manuscript_image_file = $manuscript_image_path . '/' . $file;

                                // Collect image details
                                $manuscript_image_info = pathinfo($manuscript_image_file);
                                $manuscript_image_mime_id = exif_imagetype($manuscript_image_file);
                                $manuscript_image_mime = image_type_to_mime_type($manuscript_image_mime_id);
                                $manuscript_image_size = getimagesize($manuscript_image_file);

                                // Filter image types
                                switch ($manuscript_image_mime_id) {
                                    case IMAGETYPE_JPEG:
                                    case IMAGETYPE_PNG:
                                    case IMAGETYPE_GIF:
                                    
                                        // File selected
                                        // echo '<pre>' . PHP_EOL;
                                        // echo '==> Path: ' . $manuscript_image_path . PHP_EOL;
                                        // echo '==> File selected: ' . $manuscript_image_file . PHP_EOL;
                                        // echo '==> Mime type: ' . $manuscript_image_mime . PHP_EOL;
                                        // echo '==> File details: ' . print_r($manuscript_image_info, true) . PHP_EOL;
                                        // echo '</pre>' . PHP_EOL;

                                        // Return infos based on sample:
                                        // https://iiif.io/api/image/2.1/#image-information

                                        // Complete API response
                                        $api_response->{'@id'}   = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                                        $api_response->{'@id'}  .= 'api/iiif/2/images/' . $manuscript['name'] . '-page' . $page;
                                        $api_response->protocol  = 'http://iiif.io/api/image';
                                        $api_response->width     = $manuscript_image_size[0];
                                        $api_response->height    = $manuscript_image_size[1];
                                        /* $api_response->sizes     = [];
                                        $api_response->tiles     = []; */
                                        $api_response->profile   = ['http://iiif.io/api/image/2/level2.json'];

                                        // Create available sizes list
                                        /* $defined_sizes = ['150x100', '600x400', '3000x2000'];
                                        foreach ($defined_sizes as $value) {
                                            $parsed_size = explode('x', $value);

                                            // Create size object
                                            $size = new stdClass();
                                            $size->width = $parsed_size[0];
                                            $size->height = $parsed_size[1];

                                            // Add size object to sizes array
                                            $api_response->sizes[] = $size;
                                        } */

                                        // Create tile object
                                        /* $tile = new stdClass();
                                        $tile->width = 512;
                                        $tile->{'scaleFactors'} = [1,2,4,8,16];

                                        // Add tile object to tiles array
                                        $api_response->tiles[] = $tile; */

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

                // Image path
                // $manuscript_image_path = $f3->get('ROOT') . '/data/manuscripts/' . $manuscript['name'];
                $manuscript_image_path = $f3->get('MR_DATA_PATH') . '/' . $manuscript['name'];

                // Detect file extension
                $file_extension = pathinfo($manuscript_image_path . '/' . $file, PATHINFO_EXTENSION);

                // Current file
                // echo '<pre>' . PHP_EOL;
                // echo '- Path: ' . $manuscript_image_path . PHP_EOL;
                // echo '- Current file: ' . $file . PHP_EOL;
                // echo '- Requested format: ' . $params['format'] . PHP_EOL;
                // echo '- File extension: ' . $file_extension . PHP_EOL;
                // echo '- File details: ' . print_r(pathinfo($manuscript_image_path . '/' . $file), true) . PHP_EOL;
                // echo '</pre>' . PHP_EOL;

                // Validate file extension
                if (in_array($file_extension, $valid_extensions)) {

                    // File candidate
                    // echo '<pre>' . PHP_EOL;
                    // echo '==> Path: ' . $manuscript_image_path . PHP_EOL;
                    // echo '==> File candidate: ' . $file . PHP_EOL;
                    // echo '==> File extension: ' . $file_extension . PHP_EOL;
                    // echo '==> Mime id: ' . exif_imagetype($manuscript_image_path . '/' . $file) . PHP_EOL;
                    // echo '==> Mime type: ' . mime_content_type($manuscript_image_path . '/' . $file) . PHP_EOL;
                    // echo '==> Mime type extension: ' . image_type_to_extension(exif_imagetype($manuscript_image_path . '/' . $file), false) . PHP_EOL;
                    // echo '==> Requested format: ' . $params['format'] . PHP_EOL;
                    // echo '==> File details: ' . print_r(pathinfo($manuscript_image_path . '/' . $file), true) . PHP_EOL;
                    // echo '</pre>' . PHP_EOL;

                    // Select file
                    $manuscript_image_file = $manuscript_image_path . '/' . $file;

                    // Collect image details
                    $manuscript_image_info = pathinfo($manuscript_image_file);
                    $manuscript_image_mime_id = exif_imagetype($manuscript_image_file);
                    $manuscript_image_mime = image_type_to_mime_type($manuscript_image_mime_id);
                    $manuscript_image_size = getimagesize($manuscript_image_file);

                    // Filter image types
                    switch ($manuscript_image_mime_id) {
                        case IMAGETYPE_JPEG:
                        case IMAGETYPE_PNG:
                        case IMAGETYPE_GIF:

                            // File selected
                            // echo '<pre>' . PHP_EOL;
                            // echo '==> Path: ' . $manuscript_image_path . PHP_EOL;
                            // echo '==> File selected: ' . $manuscript_image_file . PHP_EOL;
                            // echo '==> Mime type: ' . $manuscript_image_mime . PHP_EOL;
                            // echo '==> File details: ' . print_r($manuscript_image_info, true) . PHP_EOL;
                            // echo '</pre>' . PHP_EOL;

                            // Return infos based on sample:
                            // https://iiif.io/api/image/2.1/#image-information

                            // Complete API response
                            $api_response->{'@id'}   = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                            $api_response->{'@id'}  .= 'api/iiif/2/images/' . $manuscript['name'] . '-page1';
                            $api_response->protocol  = 'http://iiif.io/api/image';
                            $api_response->width     = $manuscript_image_size[0];
                            $api_response->height    = $manuscript_image_size[1];
                            /* $api_response->sizes     = [];
                            $api_response->tiles     = []; */
                            $api_response->profile   = ['http://iiif.io/api/image/2/level2.json'];

                            // Create available sizes list
                            /* $defined_sizes = ['150x100', '600x400', '3000x2000'];
                            foreach ($defined_sizes as $value) {
                                $parsed_size = explode('x', $value);

                                // Create size object
                                $size = new stdClass();
                                $size->width = $parsed_size[0];
                                $size->height = $parsed_size[1];

                                // Add size object to sizes array
                                $api_response->sizes[] = $size;
                            } */

                            // Create tile object
                            /* $tile = new stdClass();
                            $tile->width = 512;
                            $tile->{'scaleFactors'} = [1,2,4,8,16];

                            // Add tile object to tiles array
                            $api_response->tiles[] = $tile; */

                            break;
                    }
                }
            }
        }
    }
}

// Debug response
// echo '<pre>' . PHP_EOL;
// echo '==> Response: ' . print_r($api_response, true) . PHP_EOL;
// echo '</pre>' . PHP_EOL;

// Set response as JSON
header('Content-Type: text/json');

// Display IIIF Image API Schema
echo json_encode($api_response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);