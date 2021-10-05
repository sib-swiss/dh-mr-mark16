<?php
/**
 * API Routes
 * 
 * Define cache TTL in seconds
 * 
 * @see conf/config.json
 * 
 * $ttl->off: 0 (Disabled)
 * $ttl->short: 300
 * $ttl->medium: 900
 * $ttl->long: 1800
 * $ttl->images: 86400
 * 
 */
$f3->route(
    'GET /api',
    function ($f3, $params) {
        // Debug
        // echo '<pre>Params: ' . print_r($params, true) . '</pre>' . PHP_EOL;
        // echo '<pre>Root: ' . $f3->get('ROOT') . '</pre>' . PHP_EOL;
        // echo '<pre>Base: ' . $f3->get('BASE') . '</pre>' . PHP_EOL;
        // echo '<pre>Pattern: ' . $f3->get('PATTERN') . '</pre>' . PHP_EOL;
        // echo '<pre>Path: ' . $f3->get('PATH') . '</pre>' . PHP_EOL;
        // echo '<pre>MR Path:' . $f3->get('MR_PATH') . '</pre>' . PHP_EOL;
        // echo '<pre>MR Config:' . $f3->get('MR_CONFIG') . '</pre>' . PHP_EOL;
        // if ($f3->app_config->debug === true) {
        //     echo '<pre>Framework: ' . print_r($f3, true) . '</pre>' . PHP_EOL;
        // }

        // Prepare API response
        $api_response = new stdClass();
        $api_response->features = [
            'IIIF',
            'MR'
        ];

        // Set response as JSON
        header('Content-Type: text/json');

        // Display API features
        echo json_encode($api_response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    },
    $ttl->off
);
// Testing F3
$f3->route(
    'GET /api/iiif',
    function ($f3, $params) {
        // Prepare API response
        $api_response = new stdClass();
        $api_response->versions = ['2', '3'];

        // Set response as JSON
        header('Content-Type: text/json');

        // Display IIIF API versions
        echo json_encode($api_response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    },
    $ttl->off
);
$f3->route(
    [
        'GET|POST /api/iiif/v@version',
        'GET|POST /api/iiif/@version'
    ],
    function($f3, $params) {
        switch (strtolower($params['version'])) {
            default:
            case '2':
            case 'v2':
                // Build API response
                $api_response           = new stdClass();
                $api_response->altered  = true;
                $api_response->original = false;
                $api_response->spec     = '2.1';
                $api_response->syntax   = [
                    $f3->get('REALM') . '/collection/{name}',
                    $f3->get('REALM') . '/{id}/canvas/p{page-num}',
                    $f3->get('REALM') . '/{id}/info/{format}',
                    $f3->get('REALM') . '/{id}/list/{page}/{format}',
                    $f3->get('REALM') . '/{id}/manifest',
                    $f3->get('REALM') . '/{id}/sequence/{name}',
                    $f3->get('REALM') . '/{id}/res/{name}',
                    $f3->get('REALM') . '/images/{id}/info/{format}',
                    $f3->get('REALM') . '/images/{id}/page/p{page-num}/{part-num}',
                    $f3->get('REALM') . '/images/{id}/{page}',
                    $f3->get('REALM') . '/images/{id}/{page}/annotation/p{4digit-num}-image',
                    $f3->get('REALM') . '/images/{id}/{page}/comments/p{page-num}/{part-num}',
                    $f3->get('REALM') . '/images/{id}/{page}/{region}/{size}/{rotation}/{quality}/{format}',
                    $f3->get('REALM') . '/images/{id}/{region}/{size}/{rotation}/{quality}/{format}',
                    $f3->get('REALM') . '/auth/{login}',
                    $f3->get('REALM') . '/auth/{token}'
                ];
                $api_response->version  = '0.1';
                break;
            
            case '3':
            case 'v3':
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
                break;
        }

        // Set response as JSON
        header('Content-Type: text/json');

        // Display IIIF API Schema
        echo json_encode($api_response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    },
    $ttl->off
);

$f3->route(
    'GET /api/iiif/@version/collection/@name',
    // Return collection based on sample:
    // https://iiif.io/api/presentation/2.1/#collection
    function ($f3, $params) {
        // Define version path
        $api_version = (stripos($params['version'], 'v') !== false ? strtolower($params['version']) : 'v' . $params['version']);

        // Load selected API
        // require __DIR__ . '/../apis/iiif/' . $api_version . '/iiif.collection.php';
        require_once $f3->get('MR_PATH') . '/apis/iiif/' . $api_version . '/iiif.collection.php';
    },
    $ttl->off
);
$f3->route(
    'GET|POST /api/iiif/@version/@id/canvas/@page',
    function ($f3, $params) {
        // Define version path
        $api_version = (stripos($params['version'], 'v') !== false ? strtolower($params['version']) : 'v' . $params['version']);

        // Load selected API
        // require __DIR__ . '/../apis/iiif/' . $api_version . '/iiif.canvas.php';
        require_once $f3->get('MR_PATH') . '/apis/iiif/' . $api_version . '/iiif.canvas.php';
    },
    $ttl->off
);
$f3->route(
    [
        'GET|POST /api/iiif/@version/@id/info' . $f3->get('MR_IIIF_SEPARATOR') . '@format',
        'GET|POST /api/iiif/@version/images/@id/info' . $f3->get('MR_IIIF_SEPARATOR') . '@format'
    ],
    // Can't respect the standard implementation for now
    // Have to replace 'info.{format}' by 'info/{format}'

    function ($f3, $params) {
        // Define version path
        $api_version = (stripos($params['version'], 'v') !== false ? strtolower($params['version']) : 'v' . $params['version']);

        // Load selected API
        // require __DIR__ . '/../apis/iiif/' . $api_version . '/iiif.info.php';
        require_once $f3->get('MR_PATH') . '/apis/iiif/' . $api_version . '/iiif.info.php';
    },
    $ttl->off
);
$f3->route(
    'GET|POST /api/iiif/@version/@id/list/@page' . $f3->get('MR_IIIF_SEPARATOR') . '@format',
    function ($f3, $params) {
        // Define version path
        $api_version = (stripos($params['version'], 'v') !== false ? strtolower($params['version']) : 'v' . $params['version']);

        // Load selected API
        // require __DIR__ . '/../apis/iiif/' . $api_version . '/iiif.list.php';
        require_once $f3->get('MR_PATH') . '/apis/iiif/' . $api_version . '/iiif.list.php';
    },
    $ttl->off
);
$f3->route(
    'GET /api/iiif/@version/@id/manifest',
    // Return manifest based on sample:
    // https://iiif.io/api/presentation/3.0/#52-manifest
    function ($f3, $params) {
        // Define version path
        $api_version = (stripos($params['version'], 'v') !== false ? strtolower($params['version']) : 'v' . $params['version']);

        // Load selected API
        // require __DIR__ . '/../apis/iiif/' . $api_version . '/iiif.manifest.php';
        require_once $f3->get('MR_PATH') . '/apis/iiif/' . $api_version . '/iiif.manifest.php';
    },
    $ttl->off
);
$f3->route(
    'GET /api/iiif/@version/@id/sequence/@name',
    // Return manifest based on sample:
    // https://iiif.io/api/presentation/3.0/#52-manifest
    function ($f3, $params) {
        // Define version path
        $api_version = (stripos($params['version'], 'v') !== false ? strtolower($params['version']) : 'v' . $params['version']);

        // Load selected API
        // require __DIR__ . '/../apis/iiif/' . $api_version . '/iiif.sequence.php';
        require_once $f3->get('MR_PATH') . '/apis/iiif/' . $api_version . '/iiif.sequence.php';
    },
    $ttl->off
);
$f3->route(
    'GET /api/iiif/@version/@id/res/@name',
    // Return resource based on sample:
    // https://iiif.io/api/presentation/2.1/#annotation-list
    function ($f3, $params) {
        // Define version path
        $api_version = (stripos($params['version'], 'v') !== false ? strtolower($params['version']) : 'v' . $params['version']);

        // Load selected API
        // require __DIR__ . '/../apis/iiif/' . $api_version . '/iiif.resource.php';
        require_once $f3->get('MR_PATH') . '/apis/iiif/' . $api_version . '/iiif.resource.php';
    },
    $ttl->off
);
$f3->route(
    'GET|POST /api/iiif/@version/images',
    function ($f3, $params) {
        // Build API response
        $api_response = new stdClass();
        $api_response->altered = true;
        $api_response->original = false;
        $api_response->spec     = '2.1';
        $api_response->syntax   = [
            $f3->get('REALM') . '/{id}/info/{format}',
            $f3->get('REALM') . '/{id}/page/p{page-num}/{part-num}',
            $f3->get('REALM') . '/{id}/{page}',
            $f3->get('REALM') . '/{id}/{page}/annotation/p{4digit-num}-image',
            $f3->get('REALM') . '/{id}/{page}/comments/p{page-num}/{part-num}',
            $f3->get('REALM') . '/{id}/{page}/{region}/{size}/{rotation}/{quality}/{format}',
            $f3->get('REALM') . '/{id}/{region}/{size}/{rotation}/{quality}/{format}'
        ];
        $api_response->version = '0.1';

        // Set response as JSON
        header('Content-Type: text/json');

        // Display IIIF Image API Schema
        echo json_encode($api_response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    },
    $ttl->off
);
$f3->route(
    'GET|POST /api/iiif/@version/images/@id/@region/@size/@rotation/@quality' . $f3->get('MR_IIIF_SEPARATOR') . '@format',
    // Can't respect the standard implementation for now
    // Have to replace '{quality}.{format}' by '{quality}/{format}'
    // Associated token: /*

    function ($f3, $params) {
        // Define version path
        $api_version = (stripos($params['version'], 'v') !== false ? strtolower($params['version']) : 'v' . $params['version']);

        // Load selected API
        // require __DIR__ . '/../apis/iiif/' . $api_version . '/iiif.images.php';
        require_once $f3->get('MR_PATH') . '/apis/iiif/' . $api_version . '/iiif.images.php';
    },
    ($ttl->debug === true ? $ttl->off : $ttl->images)
);
/* $f3->route('GET|POST /api/iiif/@version/images/@id/@region/@size/@rotation/@quality.@format',
    function($f3, $params) {
        // Debug
        echo '<pre>' . print_r($params, true) . '</pre>' . PHP_EOL;
        echo '<pre>' . $f3->get('ROOT') . '</pre>' . PHP_EOL;
        echo '<pre>' . $f3->get('BASE') . '</pre>' . PHP_EOL;
        echo '<pre>' . $f3->get('PATTERN') . '</pre>' . PHP_EOL;
        echo '<pre>' . $f3->get('PATH') . '</pre>' . PHP_EOL;
        if ($f3->app_config->debug === true) {
            echo '<pre>' . print_r($f3, true) . '</pre>' . PHP_EOL;
        }
    }
); */
$f3->route('GET /api/mr',
    function($f3, $params) {
        // Prepare API response
        $api_response = new stdClass();
        $api_response->versions = ['1'];

        // Set response as JSON
        header('Content-Type: text/json');

        // Display MR API versions
        echo json_encode($api_response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    },
    $ttl->off
);
$f3->route('GET /api/mr/@version',
    function($f3, $params) {
        // Define version path
        $api_version = (stripos($params['version'], 'v') !== false ? strtolower($params['version']) : 'v' . $params['version']);

        // No parameters given, display API schema
        if (count($_GET) === 0) {
            // Build API response
            $api_response           = new stdClass();
            $api_response->custom   = true;
            $api_response->spec     = '1.0';
            $api_response->syntax   = [
                $f3->get('REALM') . '/?id={base64 encoded id}',
                $f3->get('REALM') . '/&folio={base64 encoded folio id}',
            ];
            $api_response->version  = '0.1';

            // Set response as JSON
            header('Content-Type: text/json');

            // Display MR API schema
            echo json_encode($api_response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }

        // Missing 'id' parameter, return 'bad request'
        elseif (isset($_GET['folio']) || !isset($_GET['id'])) {
            $f3->error(400);
        }

        // Required parameters given, load MR API
        else {
            // Load selected API
            // require __DIR__ . '/../apis/mr/' . $api_version . '/mr.new.php';
            require_once $f3->get('MR_PATH') . '/apis/mr/' . $api_version . '/mr.new.php';
            // require_once $f3->get('MR_PATH') . '/apis/mr/' . $api_version . '/mr.old.php';
        }
    }
    , $ttl->off
);


/**
 * Silvano Aldà
 * namespaced routes
 */
$f3->map(
    '/api/iiif/2-1/collection/@name',
    'iiif21\CollectionResource',
    ($ttl->debug === true ? $ttl->off : $ttl->long)
);
$f3->map(
    '/api/iiif/2-1/@id/canvas/p@pagenum',
    'iiif21\CanvasResource',
    $ttl->off
);
$f3->map(
    [
        '/api/iiif/2-1/@id/info' . $f3->get('MR_IIIF_SEPARATOR') . '@format',
        '/api/iiif/2-1/images/@id/info' . $f3->get('MR_IIIF_SEPARATOR') . '@format'
    ],
    'iiif21\ImageInfoResource',
    ($ttl->debug === true ? $ttl->off : $ttl->long)
);
$f3->map(
    '/api/iiif/2-1/@id/manifest',
    'iiif21\ManifestResource',
    ($ttl->debug === true ? $ttl->off : $ttl->long)
);
$f3->map(
    '/api/iiif/2-1/images/@id/@region/@size/@rotation/@quality' . $f3->get('MR_IIIF_SEPARATOR') . '@format',
    'iiif21\ImageResource',
    ($ttl->debug === true ? $ttl->off : $ttl->images)
);

$f3->map(
    '/api/iiif/2-1/@id/list/@page' . $f3->get('MR_IIIF_SEPARATOR') . '@format',
    'iiif21\AnnotationListResource',
    $ttl->off
);

$f3->map(
    '/api/mr/v2-1',
    'mr\v2_1\MiradorResource',
    $ttl->off
);
