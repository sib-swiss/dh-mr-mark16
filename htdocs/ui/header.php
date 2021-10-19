<?php
// Load bootstrap code
require $f3->get('MR_PATH') . '/inc/bootstrap.php';

// Page level config
if (isset($page_options) && !is_object($page_options)) {
    die('Page options must be an object.');
}

// Building page title
$page_title  = $app_config->title->base;
$page_title .= (isset($page_options->title) && !empty($page_options->title) ? ' / ' . $page_options->title : '');
$page_title .= ($app_config->debug === true ? ' ' . $app_config->title->separator . ' ' . $app_config->title->end : '');
$page_title .= ($app_config->debug === true ? ' [Debug Mode]' : '');
$page_title .= ($app_config->maintenance === true ? ' ' . $app_config->title->separator . ' [Maintenance]' : '');
?>
<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->

        <!-- SEO meta tags -->
        <!-- Robots indexing: disabled -->
        <meta name="robots" content="noindex, nofollow, nosnippet, noarchive, noimageindex, noodp">

        <?php if (isset($page_options) && $page_options->mirador === true): ?>

        <!-- overriding some viewer settings -->
        <style>
            div.manifest-info {
                display:none;
            }
            .mirador-container .content-container {
                margin-top: 0px !important;
            }
            .mirador-container .mirador-main-menu-bar {
                background-color: #484848 !important ; /* Very dark gray */
            }
            div .panel-thumbnail-view {
                background-color: #484848 !important ;
            }
            .mirador-container .mirador-osd {
                background-color: #484848 !important ;
            }
        </style>

        <?php endif; ?>

        <title><?php echo (isset($page_title) && !empty($page_title) ? $page_title : 'Page title not defined') ?></title>

        <!-- Favicon -->
        <link rel="shortcut icon" href="resources/img/mr-favicon.ico">

        <!-- Bootstrap -->
        <link rel="stylesheet" type="text/css" href="resources/css/bootstrap-4.3.1.min.css">

        <!-- Custom style -->
        <!-- <link rel="stylesheet" type="text/css" href="resources/css/style.css"> -->
        <link rel="stylesheet" type="text/css" href="resources/css/style.patched.css">

        <!-- Font-Awesome Icons -->
        <link rel="stylesheet" type="text/css" href="resources/fonts/fontawesome/css/all.css">

        <!-- Google Font -->
        <!-- <link href="https://fonts.googleapis.com/css?family=Cardo|Merriweather|Open+Sans|Droid+Serif&display=swap" rel="stylesheet"> -->

        <?php if (isset($page_options) && $page_options->mirador === true): ?>

        <!-- Mirador IIIF Viewer -->
        <link rel="stylesheet" type="text/css" href="<?php echo 'resources/js/mirador-v' . $app_config->mirador->version . '/css/mirador-combined.css'; ?>">

        <?php endif; ?>

    </head>
    <body>
        <header><?php
        require_once $f3->get('MR_PATH') . '/ui/nav.php'; ?>
        </header>

        <?php
        // Call warning message
        require_once $f3->get('MR_PATH') . '/ui/warning.php';

        // Call UI toast
        require_once $f3->get('MR_PATH') . '/ui/toasts.php';
        ?>