<?php
// Default page options
$page_options = new stdClass();
$page_options->title = $f3->get('ERROR.status');
$page_options->mirador = false;
$page_options->old_code = false;
$page_options->resources_path = '../resources'; // Might not be kept...
?>
<?php require $f3->get('MR_PATH') . '/ui/header.php'; ?>

        <div id="content" class="container">
            <div class="row">
                <!-- <div class="col-md-12"> -->
                <div class="col-md-6 offset-md-3">
                    <?php
                    echo '<h1>' . $f3->get('ERROR.status') . '</h1>' . PHP_EOL;
                    // echo '<p>Error ' . $f3->get('ERROR.code') . ' &ndash; ' . $f3->get('ERROR.text') . '</p>' . PHP_EOL;
                    echo '<p>' . $f3->get('ERROR.text') . '</p>' . PHP_EOL;
                    if ($f3->get('DEBUG') !== 0) {
                        echo '<p>Stack:</p>' . PHP_EOL;
                        echo '<pre>' . print_r($f3->get('ERROR.trace'), true) . '</pre>' . PHP_EOL;
                    }
                    ?>
                </div>
            </div>
        </div>

<?php require $f3->get('MR_PATH') . '/ui/footer.php'; ?>