<?php
/**
 * Backend Routes
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
$f3->route('GET /admin',
    function($f3, $params) {
        $f3->get('authcheck')();
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

        // Load admin or maintenance page
        if ($f3->get('MR_CONFIG')->maintenance === true) {
            require_once $f3->get('MR_PATH') . '/maintenance.php';
            // require_once __DIR__ . '/../maintenance.php';
        }
        else {
            require_once $f3->get('MR_PATH') . '/adm/interface.php';
        }
    }
    , $ttl->off
);
$f3->route('GET /admin/cache',
    function($f3, $params) {
        $f3->get('authcheck')();
        // Load cache module or maintenance page
        if ($f3->get('MR_CONFIG')->maintenance === true) {
            require_once $f3->get('MR_PATH') . '/maintenance.php';
        }
        else {
            require_once $f3->get('MR_PATH') . '/adm/opcache.php';
        }
    }
    , $ttl->off
);
$f3->route(
    [
        'GET /admin/cache/clear',
        'GET /admin/clear-cache' // Kept for retro-compatibility
    ],
    function($f3, $params) {
        $f3->get('authcheck')();
        require_once $f3->get('MR_PATH') . '/adm/clear-cache.php';
    }
    , $ttl->off
);
$f3->route('GET /admin/info',
    function($f3, $params) {
        $f3->get('authcheck')();
        // Load info module or maintenance page
        if ($f3->get('MR_CONFIG')->maintenance === true) {
            require_once $f3->get('MR_PATH') . '/maintenance.php';
        }
        else {
            require_once $f3->get('MR_PATH') . '/adm/info.php';
        }
    }
    , $ttl->off
);

/* still needed now that we are with composer?
$f3->route('GET /admin/f3',
    function($f3, $params) {
        // Load framework module or maintenance page
        if ($f3->get('MR_CONFIG')->maintenance === true) {
            require_once $f3->get('MR_PATH') . '/maintenance.php';
        }
        else {
            // Define include path
            // $f3->set('MR_PATH', $f3->get('ROOT'));

            // require_once $f3->get('MR_PATH') . '/libs/fatfree-3.7.2/index.php';
            // require_once __DIR__ . '/../libs/fatfree-3.7.2/index.php';
            $f3->reroute('/libs/fatfree-3.7.2');
        }
    }
    , $ttl->off
);
*/

$f3->route('POST /admin/parse',
    function($f3, $params) {
        $f3->get('authcheck')();
        // Load edit module or maintenance page
        if ($f3->get('MR_CONFIG')->maintenance === true) {
            require_once $f3->get('MR_PATH') . '/maintenance.php';
        }
        else {
            require_once $f3->get('MR_PATH') . '/adm/parse.php';
        }
    }
    , $ttl->off
);
$f3->route('GET /admin/download/@id',
    function($f3, $params) {
        $f3->get('authcheck')();
        // Load edit module or maintenance page
        if ($f3->get('MR_CONFIG')->maintenance === true) {
            require_once $f3->get('MR_PATH') . '/maintenance.php';
        }
        else {
            require_once $f3->get('MR_PATH') . '/adm/download.php';
        }
    }
    , $ttl->off
);
$f3->route('GET|POST /admin/dba',
    function($f3, $params) {
        //$f3->get('authcheck')();
        // Load edit module or maintenance page
        if ($f3->get('MR_CONFIG')->maintenance === true) {
            require_once $f3->get('MR_PATH') . '/maintenance.php';
        }
        else {
            // References:
            // https://www.adminer.org/en/password/
            // https://www.adminer.org/en/plugins/
            // https://www.adminer.org/en/plugins/#use
            // https://github.com/vrana/adminer/tree/v4.7.8/plugins
            // https://github.com/vrana/adminer/blob/master/editor/sqlite.php

            function adminer_object() {
                global $f3;
                require_once __DIR__ . '/../libs/adminer-4.7.8/plugins/plugin.php';
                require_once __DIR__ . '/../libs/adminer-4.7.8/plugins/login-password-less.php';
                
                class AdminerCustomization extends AdminerPlugin {
                    function loginFormField($name, $heading, $value) {
                        return parent::loginFormField($name, $heading, str_replace('value="server"', 'value="sqlite"', $value));
                    }
                    function database() {
                        global $f3;
                        return $f3->get('MR_DATA_DIR') . '/' . $f3->get('MR_CONFIG')->db->file;
                    }
                }
                
                return new AdminerCustomization(array(
                    new AdminerLoginPasswordLess(password_hash($f3->get('MR_CONFIG')->db->pass, PASSWORD_DEFAULT)),
                ));
            }
            require_once $f3->get('MR_PATH') . '/libs/adminer-4.7.8/adminer.php';
        }
    }
    , $ttl->off
);
$f3->route(
    [
        'GET /admin/edit',
        'GET /admin/edit/@id'
    ],
    function($f3, $params) {
        $f3->get('authcheck')();
        // Load edit module or maintenance page
        if ($f3->get('MR_CONFIG')->maintenance === true) {
            require_once $f3->get('MR_PATH') . '/maintenance.php';
        }
        else {
            require_once $f3->get('MR_PATH') . '/adm/edit.php';
        }
    }
    , $ttl->off
);
$f3->route('POST /admin/edit/@id',
    // TODO: Improve backend route
    'adm\ManuscriptContentResource->post',
    $ttl->off
);
$f3->route('POST /admin/update/@id',
    // TODO: Improve backend route
    'adm\ManuscriptContentResource->post',
    $ttl->off
);
$f3->route('GET /admin/sync/@id',
    function($f3, $params) {
        $f3->get('authcheck')();
        // Load edit module or maintenance page
        if ($f3->get('MR_CONFIG')->maintenance === true) {
            require_once $f3->get('MR_PATH') . '/maintenance.php';
        }
        else {
            require_once $f3->get('MR_PATH') . '/adm/sync.php';
        }
    }
    , $ttl->off
);
$f3->route('GET /admin/delete/@id',
    function($f3, $params) {
        $f3->get('authcheck')();
        // Load edit module or maintenance page
        if ($f3->get('MR_CONFIG')->maintenance === true) {
            require_once $f3->get('MR_PATH') . '/maintenance.php';
        }
        else {
            require_once $f3->get('MR_PATH') . '/adm/delete.php';
        }
    }
    , $ttl->off
);
$f3->route(
    [
        'GET /admin/view',
        'GET /admin/view/@id'
    ],
    function($f3, $params) {
        $f3->get('authcheck')();
        // Load edit module or maintenance page
        if ($f3->get('MR_CONFIG')->maintenance === true) {
            require_once $f3->get('MR_PATH') . '/maintenance.php';
        }
        else {
            require_once $f3->get('MR_PATH') . '/adm/view.php';
        }
    }
    , $ttl->off
);

$f3->route(
    [
        'GET /admin/help',
    ],
    function($f3, $params) {
        $f3->get('authcheck')();
        // Load edit module or maintenance page
        if ($f3->get('MR_CONFIG')->maintenance === true) {
            require_once $f3->get('MR_PATH') . '/maintenance.php';
        }
        else {
            require_once $f3->get('MR_PATH') . '/adm/help.php';
        }
    }
    , $ttl->off
);