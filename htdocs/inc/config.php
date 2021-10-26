<?php
// Read main JSON config file
if (is_readable(__DIR__ . '/../conf/config.json')) {
    $app_config_json = file_get_contents(__DIR__ . '/../conf/config.json');
    $app_config = json_decode($app_config_json);
    if (!is_object($app_config)) {
        die('App config must be an object.');
    }

    $languages_json = file_get_contents(__DIR__ . '/../conf/languages.json');
    $languages = json_decode($languages_json);
    if (!is_object($languages)) {
        die('languages must be an object.');
    }

    $app_config->languages = $languages;

    // $_SERVER['SERVER_NAME'] = 'localhost';
    // Add server name in the cache path
    $app_config->cache->path = $app_config->cache->path . '/' . $_SERVER['SERVER_NAME'] . '/';

    // Dirty app config patch
    // $app_config->web_root = ($_SERVER['SERVER_NAME'] === 'localhost' ? '/htdocs/' : '/');
} else {
    die('App config can\'t be loaded.');
}
