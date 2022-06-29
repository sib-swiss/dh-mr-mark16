<?php
// Load bootstrap code
// require $f3->get('MR_PATH') . '/inc/bootstrap.php';

// Page level config
if (isset($page_options) && !is_object($page_options)) {
    die('Page options must be an object.');
}

// Building page title
$page_title  = 'MR - Admin';
$page_title .= (isset($page_options->title) && !empty($page_options->title) ? ' / ' . $page_options->title : '');
// $page_title .= ($f3->get('MR_CONFIG')->debug === true ? ' ' . $f3->get('MR_CONFIG')->title->separator . ' ' . $f3->get('MR_CONFIG')->title->end : '');
$page_title .= ($f3->get('MR_CONFIG')->debug === true ? ' [Debug Mode]' : '');
$page_title .= ($f3->get('MR_CONFIG')->maintenance === true ? ' ' . $f3->get('MR_CONFIG')->title->separator . ' [Maintenance]' : '');
?>
<!doctype html>
<html lang="en">
    <head>
        <!-- Standard Meta -->
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

        <!-- Site Properties -->
        <title><?php echo $page_title; ?></title>

        <!-- Favicon -->
        <link rel="shortcut icon" href="<?php echo $f3->get('MR_PATH_WEB') . 'resources/frontend/img/mr-favicon.ico'; ?>">

        <!-- Fomantic-UI CSS -->
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.8.7/semantic.min.css" integrity="sha512-g/MzOGVPy3OQ4ej1U+qe4D/xhLwUn5l5xL0Fa7gdC258ZWVJQGwsbIR47SWMpRxSPjD0tfu/xkilTy+Lhrl3xg==" crossorigin="anonymous" />

        <!-- Custom CSS -->
        <link rel="stylesheet" type="text/css" href="<?php echo $f3->get('MR_PATH_WEB') . 'resources/backend/css/ui.css'; ?>">

        <!-- Custom Style -->
        <style type="text/css"></style>
    </head>
    <body>