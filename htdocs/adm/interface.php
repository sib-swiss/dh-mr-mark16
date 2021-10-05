<?php

use classes\Db\Clean;
use classes\Models\Manuscript;

// clean the sqlite from orphan contents db and its files
$deleted=(new Clean())->handle();

// Default page options
$page_options = new stdClass();
$page_options->title = 'Home';

// Display manuscript image in the list
$page_options->display_list_images = true;
$page_options->authorized_extensions = ['jpg', 'jpeg'];
?>
<?php require $f3->get('MR_PATH') . '/adm/ui/header.php'; ?>

<?php require $f3->get('MR_PATH') . '/adm/ui/sidebar.php'; ?>

<?php require $f3->get('MR_PATH') . '/adm/ui/nav-follow.php'; ?>

		<!-- Page Contents -->
		<div class="pusher">

			<?php require $f3->get('MR_PATH') . '/adm/ui/nav.php'; ?>

			<!-- Main content-->
			<main class="ui vertical basic very padded segment">
				<div id="variable-container" class="ui container">
					<div class="ui basic segment">
						<h2 class="ui header">
							<i class="glasses icon"></i>
							<div class="content">MR - Admin</div>
						</h2>
					</div>
					<!-- <div class="ui secondary segment">
						<h4 class="ui dividing header">Top Header</h4>
						TOP CONTENT HERE
					</div> -->
					<div class="ui blue segment">
						<h4 class="ui dividing header">Manuscripts</h4>
						<table class="ui fixed single line celled table">
							<thead>
								<tr>
									<th class="two wide center aligned">Actions</th>
									<th class="two wide center aligned">Id</th>
									<th class="three wide">Name</th>
									<th class="two wide center aligned">Status</th>
									<th>Abstract</th>
								</tr>
							</thead>
							<tbody>

								<?php foreach(Manuscript::all(['order' => 'temporal ASC']) as $manuscript): ?>

								<tr>
									<td class="center aligned">
										<!-- <a class="tooltipped" href="<?php echo 'admin/delete/' . base64_encode($manuscript->name); ?>" title="Delete"><i class="trash alternate outline icon"></i></a> -->
										<!-- <a class="tooltipped" onclick="$('.ui.delete.modal').modal('show'); jQuery.removeData($('.ui.ok.delete.manuscript'), 'id'); $('.ui.ok.delete.manuscript').data('id', '<?php echo base64_encode($manuscript->name); ?>');" title="Delete"><i class="trash alternate outline link icon"></i></a> -->
										<a class="tooltipped" href="<?php echo 'admin/edit/' . base64_encode($manuscript->name); ?>" title="Edit" data-position="bottom center"><i class="edit outline icon"></i></a>
										<a class="tooltipped" href="<?php echo 'admin/view/' . base64_encode($manuscript->name); ?>" title="View" data-position="bottom center"><i class="eye outline icon"></i></a>
										<a class="tooltipped" href="<?php echo 'admin/view/' . base64_encode($manuscript->name); ?>#/html" title="HTML" data-position="bottom center"><i class="file alternate outline icon"></i></a>
										<a class="tooltipped" href="<?php echo 'admin/view/' . base64_encode($manuscript->name); ?>#/xml" title="XML" data-position="bottom center"><i class="file code outline icon"></i></a>

										<?php if (isset($manuscript->url)): ?>
										<a class="tooltipped sync link" href="#!" title="Sync" data-position="bottom center"><i class="sync icon" data-action="nakala sync" data-id="<?php echo base64_encode($manuscript->url); ?>" data-revision="<?php echo (isset($manuscript->content) ? json_decode($manuscript->content)->version : '0'); ?>"></i></a>
										<?php else: ?>
										<a class="tooltipped" href="#!" title="Not available on Nakala" data-position="bottom center"><i class="exclamation circle icon" data-revision="<?php echo (isset($manuscript->content) ? json_decode($manuscript->content)->version : '0'); ?>"></i></a>
										<?php endif; ?>

									</td>
									<td class="center aligned"><?php echo $manuscript->getMeta('dcterm-temporal'); ?></td>
									<td>
										<h4 class="ui image header">
											
											<?php
											// ToDo: display thumb image instead of content of full image
											if (count($manuscript->contentsImage())) {
												if ($f3->get('MR_CONFIG')->images->format->base64 === true) {
													$html_image  = '<img src="data:' . $manuscript->contentsImage()[0]->imageType() . ';base64,' . $manuscript->contentsImage()[0]->imageContent() . '" ';
													$html_image .= 'class="ui mini rounded image" style="width: 25px;">' . PHP_EOL;
												}
												else {
													$html_image  = '<img src="' . $f3->get('MR_PATH_WEB') . 'api/iiif/2-1/images/' . $manuscript->name . '-page1/full/65,/0/default.jpg" ';
													$html_image .= 'class="ui mini rounded image" style="width: 25px;" loading="lazy">' . PHP_EOL;
												}
											}
											else {
												$html_image = '<img data-src="holder.js/25x50?random=yes&text=' . substr($manuscript->name, 0, 2) . '" class="ui mini rounded image">' . PHP_EOL;
											}
											echo $html_image;
											?>

											<div class="content">
												<a class="styled" href="<?php echo 'admin/view/' . base64_encode($manuscript->name); ?>" class="tooltipped" title="View manuscript" data-position="right center"><?php echo $manuscript->getDisplayname(); ?></a>
												<div class="sub header">

													<?php
													echo count($manuscript->getMetas('dcterm-creator')) . ' creator' . (count($manuscript->getMetas('dcterm-creator')) > 1 ? 's' : '') . '<br>' . PHP_EOL;
													echo count($manuscript->contentsFolios()) . ' folio' . (count($manuscript->contentsFolios()) > 1 ? 's' : '') . '<br>' . PHP_EOL;
													echo count($manuscript->contentsImage()) . ' image' . (count($manuscript->contentsImage()) > 1 ? 's' : '') . PHP_EOL;
													?>

												</div>
											</div>
										</h4>
									</td>
									<td class="center aligned"><a class="styled update link" href="#!" data-action="manuscript update status" data-id="<?php echo base64_encode($manuscript->name); ?>" data-published="<?php echo ((bool)$manuscript->published !== true ? 'false' : 'true'); ?>" data-status="<?php echo ((bool)$manuscript->published === true ? 'Published' : 'Not Published') . PHP_EOL; ?>" style="will-change: content;"><?php echo ((bool)$manuscript->published === true ? 'Published' : 'Not Published') . PHP_EOL; ?></a></td>
									<td class="tooltipped" title="<?php echo htmlentities($manuscript->getMeta('dcterm-abstract')); ?>" data-position="right center"><?php echo htmlentities($manuscript->getMeta('dcterm-abstract')); ?></td>
								</tr>

								<?php endforeach; ?>

							</tbody>
						</table>
					</div>
					<!-- <div class="ui tertiary segment">
						<h4 class="ui dividing header">Bottom Header</h4>
						BOTTOM CONTENT HERE
					</div> -->
				</div>
			</main>

			<?php require $f3->get('MR_PATH') . '/adm/ui/modals.php'; ?>
			<?php require $f3->get('MR_PATH') . '/adm/ui/footer.php'; ?>