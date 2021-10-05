<?php
/**
 * IIIF Image API v2 - PHP Implementation
 * 
 * The parameters should be interpreted as if the sequence of image manipulations were:
 * Region THEN Size THEN Rotation THEN Quality THEN Format
 * 
 * > Return status code 400 in case of error
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
$valid_extensions = ['jpg', 'jpeg'];

// Iterate over imported manuscripts
// foreach ($f3->app_manuscripts as $manuscript) {
foreach ($f3->app_instance->get_manuscripts() as $manuscript) {

    // Lookup for requested manuscript
    if ($manuscript['name'] === $manuscript_id) {

        // Debug manuscript
        // echo '<pre>' . print_r($manuscript, true) . '</pre>' . PHP_EOL;

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

                                // Filter image types
                                switch ($manuscript_image_mime_id) {
                                    case IMAGETYPE_JPEG:
                                        // File selected
                                        // echo '<pre>' . PHP_EOL;
                                        // echo '==> Path: ' . $manuscript_image_path . PHP_EOL;
                                        // echo '==> File selected: ' . $manuscript_image_file . PHP_EOL;
                                        // echo '==> Mime type: ' . $manuscript_image_mime . PHP_EOL;
                                        // echo '==> File details: ' . print_r($manuscript_image_info, true) . PHP_EOL;
                                        // echo '</pre>' . PHP_EOL;

                                        // Create new image
                                        $img = new Image($manuscript_image_file, false, '');

                                        // Image region
                                        switch ($params['region']) {
                                            case 'full':
                                                /**
                                                 * Nothing to do:
                                                 * The complete image is returned, without any cropping.
                                                 */
                                                break;

                                            case 'square':
                                                /**
                                                 * The region is defined as an area where the width and height are both equal to the length
                                                 * of the shorter dimension of the complete image.
                                                 * 
                                                 * The region may be positioned anywhere in the longer dimension of the image content at the server’s discretion,
                                                 * and centered is often a reasonable default.
                                                 */
                                                break;
                                            
                                            default:
                                                /**
                                                 * The region of the full image to be returned is specified in terms of absolute pixel values.
                                                 * 
                                                 * The value of x represents the number of pixels from the 0 position on the horizontal axis.
                                                 * The value of y represents the number of pixels from the 0 position on the vertical axis.
                                                 * 
                                                 * Thus the x,y position 0,0 is the upper left-most pixel of the image.
                                                 * w represents the width of the region and h represents the height of the region in pixels.
                                                 * 
                                                 * 
                                                 * /!\ WORKS ONLY BY PATCHING F3 CROP() METHOD /!\
                                                 * 
                                                 */

                                                // Store received coordinates
                                                list($x, $y, $w, $h) = explode(',', $params['region']);

                                                // Cast values as INT
                                                $org_x = (int)$x;
                                                $org_y = (int)$y;
                                                $org_w = (int)$w;
                                                $org_h = (int)$h;

                                                // Check received coordinates
                                                if ($org_w !== 0 && $org_h === 0) {
                                                    // Return status code 400 on error
                                                    $f3->error(
                                                        400,
                                                        "The requested region's height or width is zero, or if the region is entirely outside the bounds of the reported dimensions"
                                                    );
                                                }
                                                elseif ($org_w === 0 && $org_h !== 0) {
                                                    // Return status code 400 on error
                                                    $f3->error(
                                                        400,
                                                        "The requested region's height or width is zero, or if the region is entirely outside the bounds of the reported dimensions"
                                                    );
                                                }
                                                else {
                                                    // Crop image according to coordinates
                                                    $img->crop($org_x, $org_y, $org_w+$org_x, $org_h+$org_y);
                                                }
                                                break;
                                        }

                                        // Image size
                                        // See: https://iiif.io/api/image/2.1/#size
                                        switch ($params['size']) {
                                            case 'full':
                                                /**
                                                 * Nothing to do:
                                                 * The image or region is not scaled, and is returned at its full size.
                                                 */
                                                break;

                                            case 'max':
                                                /**
                                                 * The image or region is returned at the maximum size available, as indicated by maxWidth, maxHeight, maxArea in the profile description.
                                                 * This is the same as full if none of these properties are provided.
                                                 */
                                                break;
                                            
                                            default:
                                                /**
                                                 * Possible values:
                                                 * 
                                                 * w,   = The image or region should be scaled so that its width is exactly equal to w, and the height will be a calculated value that maintains the aspect ratio of the extracted region.
                                                 * ,h   = The image or region should be scaled so that its height is exactly equal to h, and the width will be a calculated value that maintains the aspect ratio of the extracted region.
                                                 * w,h  = The width and height of the returned image are exactly w and h. The aspect ratio of the returned image may be different than the extracted region, resulting in a distorted image.
                                                 * !w,h = The image content is scaled for the best fit such that the resulting width and height are less than or equal to the requested width and height.
                                                 *        The exact scaling may be determined by the service provider, based on characteristics including image quality and system performance.
                                                 *        The dimensions of the returned image content are calculated to maintain the aspect ratio of the extracted region.
                                                 */

                                                // Parse given size
                                                $parsed_size = explode(',', $params['size']);

                                                // Rendering possible values
                                                if ($parsed_size[0] !== '' && $parsed_size[1] === '') {
                                                    $img->resize($parsed_size[0], null, true);
                                                }
                                                elseif ($parsed_size[0] === '' && $parsed_size[1] !== '') {
                                                    $img->resize(null, $parsed_size[1], true);
                                                }
                                                elseif ($parsed_size[0] !== '' && $parsed_size[1] !== '') {
                                                    $img->resize($parsed_size[0], $parsed_size[1], false, false);
                                                }
                                                else {
                                                    $img->resize($parsed_size[0], $parsed_size[1], false);
                                                }
                                            break;
                                        }
                                    break;
                                }

                                // Clear buffer
                                ob_clean();

                                // Render image
                                switch ($manuscript_image_mime_id) {
                                    case IMAGETYPE_JPEG:
                                        $img->render( 'jpeg', 100 );
                                        break;
                                }
                            }
                        }
                    }

                    // Page found, exit the loop
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

                    // Filter image types
                    switch ($manuscript_image_mime_id) {
                        case IMAGETYPE_JPEG:
                            // File selected
                            // echo '<pre>' . PHP_EOL;
                            // echo '==> Path: ' . $manuscript_image_path . PHP_EOL;
                            // echo '==> File selected: ' . $manuscript_image_file . PHP_EOL;
                            // echo '==> Mime type: ' . $manuscript_image_mime . PHP_EOL;
                            // echo '==> File details: ' . print_r($manuscript_image_info, true) . PHP_EOL;
                            // echo '</pre>' . PHP_EOL;

                            // Create new image
                            $img = new Image($manuscript_image_file, false, '');

                            // Image region
                            switch ($params['region']) {
                                case 'full':
                                    /**
                                     * Nothing to do:
                                     * The complete image is returned, without any cropping.
                                     */
                                    break;

                                case 'square':
                                    /**
                                     * The region is defined as an area where the width and height are both equal to the length
                                     * of the shorter dimension of the complete image.
                                     * 
                                     * The region may be positioned anywhere in the longer dimension of the image content at the server’s discretion,
                                     * and centered is often a reasonable default.
                                     */
                                    break;
                                
                                default:
                                    /**
                                     * The region of the full image to be returned is specified in terms of absolute pixel values.
                                     * 
                                     * The value of x represents the number of pixels from the 0 position on the horizontal axis.
                                     * The value of y represents the number of pixels from the 0 position on the vertical axis.
                                     * 
                                     * Thus the x,y position 0,0 is the upper left-most pixel of the image.
                                     * w represents the width of the region and h represents the height of the region in pixels.
                                     * 
                                     * 
                                     * /!\ WORKS ONLY BY PATCHING F3 CROP() METHOD /!\
                                     * 
                                     */

                                    // Store received coordinates
                                    list($x, $y, $w, $h) = explode(',', $params['region']);

                                    // Cast values as INT
                                    $org_x = (int)$x;
                                    $org_y = (int)$y;
                                    $org_w = (int)$w;
                                    $org_h = (int)$h;

                                    // Check received coordinates
                                    if ($org_w !== 0 && $org_h === 0) {
                                        // Return status code 400 on error
                                        $f3->error(
                                            400,
                                            "The requested region's height or width is zero, or if the region is entirely outside the bounds of the reported dimensions"
                                        );
                                    }
                                    elseif ($org_w === 0 && $org_h !== 0) {
                                        // Return status code 400 on error
                                        $f3->error(
                                            400,
                                            "The requested region's height or width is zero, or if the region is entirely outside the bounds of the reported dimensions"
                                        );
                                    }
                                    else {
                                        // Crop image according to coordinates
                                        $img->crop($org_x, $org_y, $org_x + $org_w, $org_y + $org_h);
                                    }

                                    /**
                                     * Testing code from:
                                     * https://github.com/conlect/image-iiif/blob/master/src/Filters/RegionFilter.php
                                     * 
                                     * Result: Bad idea...
                                     *  - Reason 1: when it works, their calculations simply always return the original image size
                                     *  - Reason 2: their implementation is reversing $x,$y values those from $w,$h
                                     *  - Consequences: it creates coordinates like 3567,596,0,0 instead of 0,0,3567,596
                                     * 
                                     * All testing code will be removed, just kept here for reference
                                     */

                                    /* echo '<pre>' . PHP_EOL;
                                    var_dump(
                                        explode(',', $params['region']),
                                        [$x, $y, $w, $h],
                                        [$org_x, $org_y, $org_w, $org_h],
                                        substr($org_x, 4)
                                    );
                                    echo '</pre>' . PHP_EOL; */

                                    // iiif - x,y,w,h
                                    /* $new_x = (int)round($width * (int)substr($org_x, 4) / 100);
                                    // $new_x = (int)round($width * $org_x / 100);
                                    $new_y = (int)round($height * $org_y / 100);
                                    $new_w = (int)round($width * $org_w / 100);
                                    $new_h = (int)round($height * $org_h / 100);

                                    if ($org_w + (int)substr($org_x, 4) > 100) {
                                    // if ($org_w + $org_x > 100) {
                                        $new_w = $width - $new_x;
                                    }

                                    if ($org_h + $org_y > 100) {
                                        $new_h = $height - $new_y;
                                    } */

                                    /* echo '<pre>' . PHP_EOL;
                                    echo ' - Original image dimensions: ' . $width . 'x' . $height . PHP_EOL;
                                    echo ' - Received values: ' . $x . ', ' . $y . ', ' . $w . ', ' . $h . PHP_EOL;
                                    echo ' - Saved values: ' . $org_x . ', ' . $org_y . ', ' . $org_w . ', ' . $org_h . PHP_EOL;
                                    echo ' - Modified values: ' . $new_x . ', ' . $new_y . ', ' . $new_w . ', ' . $new_h . PHP_EOL;
                                    echo ' - F3 execution order: ' . $new_x . ', ' . $new_y . ', ' . $new_w . ', ' . $new_h . PHP_EOL;
                                    echo ' - ExtLib execution order: ' . $new_w . ', ' . $new_h . ', ' . $new_x . ', ' . $new_y . PHP_EOL;
                                    echo '</pre>' . PHP_EOL;
                                    exit; */

                                    // $img->crop($org_x, $org_y, $org_w, $org_h); (GOOD ONE)
                                    // $img->crop($new_x, $new_y, $new_w, $new_h); (BAD ONE)

                                    // intervention - w,h,x,y
                                    // $img->crop($new_w, $new_h, $new_x, $new_y); (TOTAL NONSENSE)
                                    break;
                            }

                            // Image size
                            // See: https://iiif.io/api/image/2.1/#size
                            switch ($params['size']) {
                                case 'full':
                                    /**
                                    * Nothing to do:
                                    * The image or region is not scaled, and is returned at its full size.
                                    */
                                    break;

                                case 'max':
                                    /**
                                    * The image or region is returned at the maximum size available, as indicated by maxWidth, maxHeight, maxArea in the profile description.
                                    * This is the same as full if none of these properties are provided.
                                    */
                                    break;
                                
                                default:
                                    /**
                                    * Possible values:
                                    * 
                                    * w,   = The image or region should be scaled so that its width is exactly equal to w, and the height will be a calculated value that maintains the aspect ratio of the extracted region.
                                    * ,h   = The image or region should be scaled so that its height is exactly equal to h, and the width will be a calculated value that maintains the aspect ratio of the extracted region.
                                    * w,h  = The width and height of the returned image are exactly w and h. The aspect ratio of the returned image may be different than the extracted region, resulting in a distorted image.
                                    * !w,h = The image content is scaled for the best fit such that the resulting width and height are less than or equal to the requested width and height.
                                    *        The exact scaling may be determined by the service provider, based on characteristics including image quality and system performance.
                                    *        The dimensions of the returned image content are calculated to maintain the aspect ratio of the extracted region.
                                    */

                                    // Parse given size
                                    $parsed_size = explode(',', $params['size']);

                                    // Rendering possible values
                                    if ($parsed_size[0] !== '' && $parsed_size[1] === '') {
                                        $img->resize($parsed_size[0], null);
                                    }
                                    elseif ($parsed_size[0] === '' && $parsed_size[1] !== '') {
                                        $img->resize(null, $parsed_size[1]);
                                    }
                                    elseif ($parsed_size[0] !== '' && $parsed_size[1] !== '') {
                                        $img->resize($parsed_size[0], $parsed_size[1], false, false);
                                    }
                                    else {
                                        $img->resize($parsed_size[0], $parsed_size[1], false);
                                    }
                                break;
                            }
                        break;
                    }

                    // Clear buffer
                    ob_clean();

                    // Render image
                    switch ($manuscript_image_mime_id) {
                        case IMAGETYPE_JPEG:
                            $img->render( 'jpeg', 100 );
                            break;
                    }
                }
            }
        }
    }
}