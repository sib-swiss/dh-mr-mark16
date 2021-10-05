<?php
// Call F3
// $f3 = require(__DIR__ . '/../../../libs/fatfree-3.7.2/lib/base.php');

// Call bootstrap code
require_once __DIR__ . '/../../../inc/bootstrap.php';

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

// Testing F3
// $f3->route('GET /',
//     function() {
//         echo 'Hello, world!<br>From: ' . __FILE__;
//     }
// );

// Custom Manuscript IIIF API 0.1
// IIIF Image Specs: https://iiif.io/api/image/3.0/
// IIIF Presentation Specs: https://iiif.io/api/presentation/3.0/
$f3->route('GET /',
    function($f3) {
        // Build API response
        $api_response           = new stdClass();
        $api_response->altered  = true;
        $api_response->original = false;
        $api_response->spec     = '3.0';
        $api_response->syntax   = [
            $f3->get('REALM') . '/collection',
            $f3->get('REALM') . '/{id}/canvas/p{page-num}',
            $f3->get('REALM') . '/{id}/info/{format}',
            $f3->get('REALM') . '/{id}/manifest',
            $f3->get('REALM') . '/{id}/page/p{page-num}/{part-num}',
            $f3->get('REALM') . '/{id}/{page}',
            $f3->get('REALM') . '/{id}/{page}/annotation/p{4digit-num}-image',
            $f3->get('REALM') . '/{id}/{page}/comments/p{page-num}/{part-num}',
            $f3->get('REALM') . '/{id}/{page}/{region}/{size}/{rotation}/{quality}/{format}',
            $f3->get('REALM') . '/{id}/{region}/{size}/{rotation}/{quality}/{format}',
            $f3->get('REALM') . '/{auth}/{login}',
            $f3->get('REALM') . '/{auth}/{token}',
        ];
        $api_response->version  = '0.1';

        // Set response as JSON
        header('Content-Type: text/json');

        // Display IIIF Image API Schema
        echo json_encode($api_response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
);

// Test collection route
$f3->route('GET /collection',
    // Return collection based on sample:
    // https://iiif.io/api/presentation/3.0/#51-collection
    function($f3) {
        // Build API response
        $api_response                               = new stdClass();
        $api_response->{'@context'}                 = 'http://iiif.io/api/presentation/3/context.json';
        $api_response->id                           = $f3->get('REALM') . '/top';
        $api_response->type                         = 'Collection';
        $api_response->label                        = new stdClass();
        $api_response->label->en                    = ['Collection for the Mark16 project'];
        $api_response->summary                      = new stdClass();
        $api_response->summary->en                  = ['This is the IIIF collection for the Mark16 project'];
        $api_response->requiredStatement            = new stdClass();
        $api_response->requiredStatement->label     = new stdClass();
        $api_response->requiredStatement->label->en = ['Attribution'];
        $api_response->requiredStatement->value     = new stdClass();
        $api_response->requiredStatement->value->en = ['Provided by SIB / DH+ Group'];
        
        // Build collection items
        $api_response->items = [];

        // Iterate over imported manuscripts
        foreach ($f3->app_manuscripts as $manuscript) {
            // Generate item object
            $item            = new stdClass();
            $item->id        = str_replace('collection', $manuscript['name'], $f3->get('REALM')) . '/manifest';
            $item->type      = 'Manifest';
            $item->label     = new stdClass();
            $item->label->en = ['Manifest for ' . $manuscript['name']];

            // TODO: Add some thumbnails
            $item->thumbnail = [];

            // Import item object to collection
            $api_response->items[] = $item;
        }

        // Set response as JSON
        header('Content-Type: text/json');

        // Display IIIF Image API Schema
        echo json_encode($api_response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
);

// Test manifest route
$f3->route('GET /@id/manifest',
    // Return manifest based on sample:
    // https://iiif.io/api/presentation/3.0/#52-manifest
    function($f3, $params) {
        // Build API response
        $api_response               = new stdClass();
        $api_response->{'@context'} = 'http://iiif.io/api/presentation/3/context.json';
        $api_response->id           = $f3->get('REALM');
        $api_response->type         = 'Manifest';

        // Iterate over imported manuscripts
        foreach ($f3->app_manuscripts as $manuscript) {
            if ($manuscript['name'] === $params['id']) {
                // Set manuscript name
                $manuscript_name = $manuscript['name'];

                // Add label
                $api_response->label     = new stdClass();
                $api_response->label->en = [$manuscript_name];
                $api_response->metadata  = [];

                // Get manuscript data
                $manuscript_data = $f3->app_instance->get_manuscript($manuscript_name);

                // Get manuscript dcterms (main folio only)
                if (count($manuscript['sub_folder']) > 0) {
                    $dcterms = $manuscript_data[$manuscript_name][0]['data']['dcterms'];
                }
                else {
                    $dcterms = $manuscript_data[$manuscript_name]['data']['dcterms'];
                }

                // Iterate over manuscript dcterms
                foreach ($dcterms as $key => $value) {
                    switch ($key) {
                        case 'dcterm-creator':
                            $meta              = new stdClass();
                            $meta->label       = new stdClass();
                            $meta->label->en   = ['Author'];
                            $meta->value       = new stdClass();
                            $meta->value->none = [
                                (string)$value[0]
                            ];

                            // Add built metas
                            $api_response->metadata[] = $meta;
                            break;

                        case 'dcterm-provenance':
                            $meta            = new stdClass();
                            $meta->label     = new stdClass();
                            $meta->label->en = ['Published'];
                            $meta->value     = new stdClass();
                            $meta->value->en = [
                                (string)$value[0]
                            ];

                            // Add built metas
                            $api_response->metadata[] = $meta;
                            break;

                        case 'dcterm-subject':
                            $meta            = new stdClass();
                            $meta->label     = new stdClass();
                            $meta->label->en = ['Notes'];
                            $meta->value     = new stdClass();
                            $meta->value->en = [];

                            foreach ($dcterms[$key] as $subject) {
                                $meta->value->en[] = (string)$subject;
                            }

                            // Add built metas
                            $api_response->metadata[] = $meta;
                            break;

                        case 'dcterm-isVersionOf':
                            $meta              = new stdClass();
                            $meta->label       = new stdClass();
                            $meta->label->en   = ['Source'];
                            $meta->value       = new stdClass();
                            $meta->value->none = [
                                '<span>From: <a href="' . $value[0] . '">' . $dcterms['dcterm-title'][0] . '</a></span>'
                            ];

                            // Add built metas
                            $api_response->metadata[] = $meta;
                            break;
                    }
                }

                // Build summary
                $summary  = $manuscript_name . ', written by ' . $dcterms['dcterm-creator'][0];
                $summary .= (isset($dcterms['dcterm-contributor']) && !empty($dcterms['dcterm-contributor'][0])
                    ? ' and ' . $dcterms['dcterm-contributor'][0]
                    : ''
                );
                $summary .= ', originaly published in ' . $dcterms['dcterm-subject'][4];
                $summary .= ' around ' . $dcterms['dcterm-date'][0];

                // Add summary
                $api_response->summary = new stdClass();
                $api_response->summary->en = [$summary];

                // Add thumbnails reference to create later
                $api_response->thumbnail = [];
                if (count($manuscript['sub_folder']) > 0) {
                    $loop = 0;
                    foreach ($manuscript['sub_folder'] as $sub_folder_name) {
                        foreach ($manuscript['content'] as $file) {
                            if (stripos($file, $sub_folder_name) !== false) {
                                if (stripos($file, '.jpg') !== false || stripos($file, '.jpeg') !== false) {
                                    // Increment loop counter
                                    $loop++;

                                    // Build thumbnail object
                                    $thumbnail          = new stdClass();
                                    $thumbnail->id      = str_replace('manifest', 'page' . $loop, $f3->get('REALM'));
                                    $thumbnail->id     .= '/full/80,100/0/default/jpg';
                                    $thumbnail->type    = 'Image';
                                    $thumbnail->format  = 'image/jpeg';
                                    $thumbnail->service = [];
        
                                    // Build image service definition
                                    $image_service          = new stdClass();
                                    $image_service->id      = str_replace('manifest', 'page' . $loop, $f3->get('REALM'));
                                    $image_service->type    = 'ImageService3';
                                    $image_service->profile = 'level1';
        
                                    // Add defined service to thumbnail
                                    $thumbnail->service[]   = $image_service;
        
                                    // Add thumbnail to API response
                                    $api_response->thumbnail[] = $thumbnail;
                                }
                            }
                        }
                    }
                }
                else {
                    foreach ($manuscript['content'] as $file) {
                        if (stripos($file, '.jpg') !== false || stripos($file, '.jpeg') !== false) {
                            $thumbnail          = new stdClass();
                            $thumbnail->id      = str_replace('manifest', 'page1', $f3->get('REALM'));
                            $thumbnail->id     .= '/full/80,100/0/default/jpg';
                            $thumbnail->type    = 'Image';
                            $thumbnail->format  = 'image/jpeg';
                            $thumbnail->service = [];

                            // Build image service definition
                            $image_service          = new stdClass();
                            $image_service->id      = str_replace('manifest', 'page1', $f3->get('REALM'));
                            $image_service->type    = 'ImageService3';
                            $image_service->profile = 'level1';

                            // Add defined service to thumbnail
                            $thumbnail->service[]   = $image_service;

                            // Add thumbnail to API response
                            $api_response->thumbnail[] = $thumbnail;
                        }
                    }
                }

                // Add presentation details
                switch ($dcterms['dcterm-language'][0]) {
                    case 'Arabic':
                        $api_response->viewingDirection = 'right-to-left';
                        break;
                    
                    default:
                        $api_response->viewingDirection = 'left-to-right';
                        break;
                }
                $api_response->behavior = ['paged'];
                $api_response->navDate  = (string)$dcterms['dcterm-created'][0];

                // Add rights
                $api_response->rights = (string)$dcterms['dcterm-license'][0];
                $api_response->requiredStatement = new stdClass();
                $api_response->requiredStatement->label = new stdClass();
                $api_response->requiredStatement->label->en = ['Attribution'];
                $api_response->requiredStatement->value = new stdClass();
                $api_response->requiredStatement->value->en = [
                    'Provided by ' . (string)$dcterms['dcterm-provenance'][0]
                ];

                // Add provider
                $api_response->provider = [];
                $provider               = new stdClass();
                $provider->id           = $f3->get('SCHEME') . '://'; // Protocol
                $provider->id          .= $f3->get('SERVER.HTTP_HOST'); // Host:Port
                $provider->id          .= $f3->get('MR_PATH_WEB'); // Web Root
                $provider->id          .= 'about'; // About page
                $provider->type         = 'Agent';
                $provider->label        = new stdClass();
                $provider->label->en    = ['SIB / DH+ Group'];

                // Add provider homepage
                $provider->homepage           = [];
                $provider_homepage            = new stdClass();
                $provider_homepage->id        = 'https://sib.swiss';
                $provider_homepage->type      = 'Text';
                $provider_homepage->label     = new stdClass();
                $provider_homepage->label->en = ['SIB Homepage'];
                $provider_homepage->format    = 'text/html';

                // TODO: Add provider homepage logo
                $provider_homepage->logo = [];

                // Add homepage to provider object
                $provider->homepage[] = $provider_homepage;

                // Add provider object to array
                $api_response->provider[] = $provider;
                
                // Add manuscript homepage
                $api_response->homepage         = [];
                $manuscript_homepage            = new stdClass();
                $manuscript_homepage->id        = $f3->get('SCHEME') . '://'; // Protocol
                $manuscript_homepage->id       .= $f3->get('SERVER.HTTP_HOST'); // Host:Port
                $manuscript_homepage->id       .= $f3->get('MR_PATH_WEB'); // Web Root
                $manuscript_homepage->id       .= 'show/' . $manuscript_name; // Manuscript route
                $manuscript_homepage->type      = 'Text';
                $manuscript_homepage->label     = new stdClass();
                $manuscript_homepage->label->en = [
                    'Home page for ' . $manuscript_name . ' manuscript'
                ];
                $manuscript_homepage->format    = 'text/html';

                // Add manuscript homepage to the main homepage
                $api_response->homepage[] = $manuscript_homepage;

                // List of Canvases (where are linked the images)
                $api_response->items = [];

                // Gather all sub folders
                $sub_folder_index = 0;
                if (count($manuscript['sub_folder']) > 0) {
                    // Iterate over all discovered folios
                    foreach ($manuscript['sub_folder'] as $folio_name) {
                        // Increment sub folder index
                        $sub_folder_index++;

                        // Build item object
                        $item        = new stdClass();
                        $item->id    = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                        $item->id   .= 'api/iiif/' . $manuscript_name . '/canvas/p' . $sub_folder_index;
                        $item->type  = 'Canvas';
                        $item->label = new stdClass();
                        $item->label->none = [
                            $folio_name
                        ];

                        // Gather image dimensions
                        foreach ($manuscript['content'] as $file) {
                            if (stripos($file, $folio_name) !== false) {
                                if (stripos($file, '.jpg') !== false || stripos($file, '.jpeg') !== false) {
                                    // Define image path
                                    $manuscript_path       = __DIR__ . '/../../../../data/manuscripts/' . $manuscript_name;
                                    $manuscript_image      = $file;
                                    $manuscript_image_path = $manuscript_path . '/' . $file;

                                    // Get image dimensions
                                    $image_size = getimagesize($manuscript_image_path);

                                    // Set image dimensions to item object
                                    $item->width  = $image_size[0];
                                    $item->height = $image_size[1];
                                }
                            }
                        }

                        // Add image items
                        $item->items = [];

                        // Add annotations structure to items
                        $annotations        = new stdClass();
                        $annotations->id    = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                        $annotations->id   .= 'api/iiif/' . $manuscript_name . '/page/p' . $sub_folder_index . '/1';
                        $annotations->type  = 'AnnotationPage';
                        $annotations->items = [];

                        // Add image to annotations items
                        $annotations_image                = new stdClass();
                        $annotations_image->id            = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                        $annotations_image->id           .= 'api/iiif/' . $manuscript_name . '/annotation/p000' . $sub_folder_index . '-image';
                        $annotations_image->type          = 'Annotation';
                        $annotations_image->motivation    = 'painting';
                        $annotations_image->body          = new stdClass();
                        $annotations_image->body->id      = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                        $annotations_image->body->id     .= 'api/iiif/' . $manuscript_name . '/page' . $sub_folder_index;
                        $annotations_image->body->id     .= '/full/max/0/default/jpg';
                        $annotations_image->body->type    = 'Image';
                        $annotations_image->body->format  = 'image/jpeg';
                        $annotations_image->body->service = [];

                        // Add image service
                        $image_service          = new stdClass();
                        $image_service->id      = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                        $image_service->id     .= 'api/iiif/' . $manuscript_name . '/page' . $sub_folder_index;
                        $image_service->type    = 'ImageService3';
                        $image_service->profile = 'level2';
                        $image_service->service = [];

                        // Add auth service (sample)
                        $auth_service            = new stdClass();
                        $auth_service->{'@id'}   = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                        $auth_service->{'@id'}  .= 'api/iiif/auth/login';
                        $auth_service->{'@type'} = 'AuthCookieService1';

                        // Add auth service to image service
                        $image_service->service[] = $auth_service;

                        // Add image service to body service
                        $annotations_image->body->service[] = $image_service;

                        // Add image dimensions to annotations image
                        $annotations_image->body->width  = $image_size[0];
                        $annotations_image->body->height = $image_size[1];

                        // Add target canvas
                        $annotations_image->target  = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                        $annotations_image->target .= 'api/iiif/' . $manuscript_name . '/canvas/p' . $sub_folder_index;

                        // Add annotations image to annotations items
                        $annotations->items[] = $annotations_image;

                        // Add annotation links
                        $annotations->annotations = [];

                        // Define annotation link object
                        $annotation_link       = new stdClass();
                        $annotation_link->id   = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                        $annotation_link->id  .= 'api/iiif/' . $manuscript_name . '/comments/p' . $sub_folder_index . '/1';
                        $annotation_link->type = 'AnnotationPage';

                        // Add annotation link
                        $annotations->annotations[] = $annotation_link;

                        // Add annotations to items
                        $item->items[] = $annotations;

                        // Add items to main item object
                        $api_response->items[] = $item;
                    }
                }

                // Read the single one
                else {
                    // Build item object
                    $item = new stdClass();
                    $item->id    = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                    $item->id   .= 'api/iiif/' . $manuscript_name . '/canvas/p1';
                    $item->type  = 'Canvas';
                    $item->label = new stdClass();
                    $item->label->none = [
                        $manuscript_name
                    ];

                    // Gather image dimensions
                    foreach ($manuscript['content'] as $file) {
                        if (stripos($file, '.jpg') !== false || stripos($file, '.jpeg') !== false) {
                            // Define image path
                            $manuscript_path       = __DIR__ . '/../../../../data/manuscripts/' . $manuscript_name;
                            $manuscript_image      = $file;
                            $manuscript_image_path = $manuscript_path . '/' . $file;

                            // Get image dimensions
                            $image_size = getimagesize($manuscript_image_path);

                            // Set image dimensions to item object
                            $item->width  = $image_size[0];
                            $item->height = $image_size[1];
                        }
                    }

                    // Add image items
                    $item->items = [];

                    // Add annotations structure to items
                    $annotations        = new stdClass();
                    $annotations->id    = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                    $annotations->id   .= 'api/iiif/' . $manuscript_name . '/page/p1/1';
                    $annotations->type  = 'AnnotationPage';
                    $annotations->items = [];

                    // Add image to annotations items
                    $annotations_image                = new stdClass();
                    $annotations_image->id            = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                    $annotations_image->id           .= 'api/iiif/' . $manuscript_name . '/annotation/p0001-image';
                    $annotations_image->type          = 'Annotation';
                    $annotations_image->motivation    = 'painting';
                    $annotations_image->body          = new stdClass();
                    $annotations_image->body->id      = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                    $annotations_image->body->id     .= 'api/iiif/' . $manuscript_name . '/page1';
                    $annotations_image->body->id     .= '/full/max/0/default/jpg';
                    $annotations_image->body->type    = 'Image';
                    $annotations_image->body->format  = 'image/jpeg';
                    $annotations_image->body->service = [];

                    // Add image service
                    $image_service          = new stdClass();
                    $image_service->id      = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                    $image_service->id     .= 'api/iiif/' . $manuscript_name . '/page1';
                    $image_service->type    = 'ImageService3';
                    $image_service->profile = 'level2';
                    $image_service->service = [];

                    // Add auth service (sample)
                    $auth_service            = new stdClass();
                    $auth_service->{'@id'}   = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                    $auth_service->{'@id'}  .= 'api/iiif/auth/login';
                    $auth_service->{'@type'} = 'AuthCookieService1';

                    // Add auth service to image service
                    $image_service->service[] = $auth_service;

                    // Add image service to body service
                    $annotations_image->body->service[] = $image_service;

                    // Add image dimensions to annotations image
                    $annotations_image->body->width  = $image_size[0];
                    $annotations_image->body->height = $image_size[1];

                    // Add target canvas
                    $annotations_image->target  = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                    $annotations_image->target .= 'api/iiif/' . $manuscript_name . '/canvas/p1';

                    // Add annotations image to annotations items
                    $annotations->items[] = $annotations_image;

                    // Add annotation links
                    $annotations->annotations = [];

                    // Define annotation link object
                    $annotation_link       = new stdClass();
                    $annotation_link->id   = $f3->get('SCHEME') . '://' . $f3->get('SERVER.HTTP_HOST') . $f3->get('MR_PATH_WEB');
                    $annotation_link->id  .= 'api/iiif/' . $manuscript_name . '/comments/p1/1';
                    $annotation_link->type = 'AnnotationPage';

                    // Add annotation link
                    $annotations->annotations[] = $annotation_link;

                    // Add annotations to items
                    $item->items[] = $annotations;

                    // Add items to main item object
                    $api_response->items[] = $item;
                }
            }
        }

        // Set response as JSON
        header('Content-Type: text/json');

        // Display IIIF Image API Schema
        echo json_encode($api_response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
);

// Test image route
$f3->route('GET|POST /@id/@region/@size/@rotation/*',
    // Can't respect the standard implementation for now
    // Have to replace '{quality}.{format}' by '{quality}/{format}'
    // Associated token: *

    function($f3, $params) {
        echo '<pre>' . print_r($params, true) . '</pre>' . PHP_EOL;
        if ($f3->app_config->debug === true) {
            echo '<pre>' . print_r($f3, true) . '</pre>' . PHP_EOL;
        }
    }
);


// Init routing engine
$f3->run();