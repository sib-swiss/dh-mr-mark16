
<?php


use classes\Models\Manuscript;

require __DIR__ . '/../vendor/autoload.php';
$f3 = require __DIR__ . '/../inc/bootstrap-app.php';

$id = 50;
$manuscript = Manuscript::findBy('id', $id);
if (!$manuscript) {
    die("\n\n   Manuscript with id {$id} not found\n\n");
}
$removed = $manuscript->remove();
var_dump([
    'manuscriptt' => $manuscript->name,
    'removed' => $removed
]);
// ->erase();