<?php
// autoload using composer vendor autoload
require __DIR__ . '/../vendor/autoload.php';

//  bootstrapping app using the composer workflow
$f3 = require __DIR__ . '/../inc/bootstrap-app.php';

// Call defined routes
require __DIR__ . '/../inc/routes.php';

// Init routing engine
$f3->run();
