<?php
echo '<!--' . PHP_EOL;
echo 'Pattern: ' . $f3->get('PATTERN') . PHP_EOL;
echo '-->' . PHP_EOL;

// Include required modals based on the current route
switch ($f3->get('PATTERN')) {
    case '/admin':
        require $f3->get('MR_PATH') . '/adm/ui/modals/modal.add.manuscript.php';
        require $f3->get('MR_PATH') . '/adm/ui/modals/modal.delete.php';
        break;

    case '/admin/edit/@id':
        require $f3->get('MR_PATH') . '/adm/ui/modals/modal.add.manuscript.php';
        require $f3->get('MR_PATH') . '/adm/ui/modals/modal.add.partner.php';
        break;

    /* case '/admin/view/@id':
        require $f3->get('MR_PATH') . '/adm/ui/modals/modal.add.manuscript.php';
        break; */

    default:
        require $f3->get('MR_PATH') . '/adm/ui/modals/modal.add.manuscript.php';
        break;
}