<?php
if (isset($app_config) && $app_config->debug === true) {
    echo PHP_EOL . '<!-- Server: ' . PHP_EOL;
    print_r($_SERVER);
    echo '-->' . PHP_EOL;

    if (isset($f3)) {
        echo PHP_EOL . '<!-- Framework: ' . PHP_EOL;
        print_r(preg_replace('/<!--([\s\S])*?-->/', '', $f3));
        echo '-->' . PHP_EOL;
    }
    
    echo PHP_EOL . '<!-- Included Files: ' . PHP_EOL;
    foreach (get_included_files() as $included_file) {
        echo ' - ' . $included_file . PHP_EOL;
    }
    echo '-->' . PHP_EOL;
    echo '<!-- Total: ' . count(get_included_files()) . ' -->' . PHP_EOL;
}