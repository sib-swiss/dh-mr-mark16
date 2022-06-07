<?php

namespace Tests;

use classes\Models\Manuscript;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../inc/bootstrap-app.php';

//$manuscript = Manuscript::findBy('name', 'GA1230');
$manuscript = Manuscript::findBy('name', 'GA1230');
//$clean = $manuscript->clean();

foreach ($manuscript->contentsHtml() as $contentsHtml) {
    //echo "\n FName: ".$contentsHtml->getFolioName();
    if('GA1230_f.147v' === $contentsHtml->getFolioName()){
        var_dump($contentsHtml->getAlteredHtml());
    }
}
exit;
dd([
    $clean,
    count($manuscript->contentsImage())
]);
