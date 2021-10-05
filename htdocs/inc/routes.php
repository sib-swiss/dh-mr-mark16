<?php
// Load framework
//require 'framework.php';

// Define cache TTL in seconds
// 0 = Disabled
$ttl = $f3->get('MR_CONFIG')->routes->ttl;

// Custom error handler
$f3->set(
    'ONERROR',
    function ($f3) {
        echo '<h1>' . $f3->get('ERROR.status') . '</h1>' . PHP_EOL;
        // echo '<p>Error ' . $f3->get('ERROR.code') . ' &ndash; ' . $f3->get('ERROR.text') . '</p>' . PHP_EOL;
        echo '<p>' . $f3->get('ERROR.text') . '</p>' . PHP_EOL;
        if ($f3->get('DEBUG') !== 0) {
            echo '<p>Stack:</p>' . PHP_EOL;
            echo '<pre>' . print_r($f3->get('ERROR.trace'), true) . '</pre>' . PHP_EOL;
        }
    }
);

// Include Frontend routes
require $f3->get('MR_PATH') . '/routes/frontend.php';

// Include Backend routes
require $f3->get('MR_PATH') . '/routes/backend.php';

// Include API routes
require $f3->get('MR_PATH') . '/routes/api.php';
