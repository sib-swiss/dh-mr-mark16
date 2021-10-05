<?php

use classes\Models\Manuscript;

// Default page options
$page_options = new stdClass();
$page_options->title = 'Manual';

$file = $f3->read($f3->get('MR_PATH') . '/../doc/ADMIN-WEB-INTERFACE.md');
$html = \Markdown::instance()->convert($file);

?>
<?php require_once $f3->get('MR_PATH') . '/adm/ui/header.php'; ?>

<?php require_once $f3->get('MR_PATH') . '/adm/ui/nav-follow.php'; ?>

<?php require_once $f3->get('MR_PATH') . '/adm/ui/sidebar.php'; ?>

<!-- Page Contents -->
<div class="pusher">

	<?php require_once $f3->get('MR_PATH') . '/adm/ui/nav.php'; ?>

	<!-- Main content-->
	<main class="ui vertical basic padded segment">


		<div class="ui container basic segment">
			<?php echo $html; ?>
		</div>

	</main>

	<?php require_once $f3->get('MR_PATH') . '/adm/ui/modals.php'; ?>
	<?php require_once $f3->get('MR_PATH') . '/adm/ui/footer.php'; ?>