<?php
use classes\Models\Manuscript;

// Default page options
$page_options = new stdClass();
$page_options->title = 'Edit';

// Prepare manuscript selector
if (isset($params['id'])) {
	// Lookup for existing manuscript
	if ($manuscript = Manuscript::findBy('name', base64_decode($params['id']))) {
		$manuscript_display_name = $manuscript->getDisplayname();
	}
	else {
		$manuscript_display_name = 'Error: Manuscript not found';
	}

	// Append manuscript name to page title
	$page_options->title .= ' / ' . $manuscript_display_name;
}
?>
<?php require_once $f3->get('MR_PATH') . '/adm/ui/header.php'; ?>

<?php require_once $f3->get('MR_PATH') . '/adm/ui/nav-follow.php'; ?>

<?php require_once $f3->get('MR_PATH') . '/adm/ui/sidebar.php'; ?>

		<!-- Page Contents -->
		<div class="pusher">

			<?php require_once $f3->get('MR_PATH') . '/adm/ui/nav.php'; ?>

			<!-- Main content-->
			<main class="ui vertical basic padded segment">

				<?php if (!$manuscript): ?>

				<div class="ui container basic segment">
					<div class="ui form">
						<div class="field">
							<label>Manuscript</label>
							<select class="ui search selection dropdown" id="search-manuscript">
								<option value="">Select a manuscript to edit</option>

								<?php foreach (Manuscript::all(['order' => 'temporal ASC']) as $m): ?>
								<option value="<?php echo $f3->get('MR_PATH_WEB') . 'admin/edit/' . base64_encode($m->name); ?>"><?php echo $m->getDisplayname(); ?></option>
								<?php endforeach; ?>

							</select>
						</div>
					</div>
				</div>

				<?php else: ?>

				<div class="ui fluid container basic segment">
					<!-- Edit warning -->
					<!-- TODO: Display warning only once -->
					<div class="ui warning message">
						<i class="close icon"></i>
						<div class="header">Warning</div>
						All the changes made here won't be replicated on Nakala!
					</div>

					<!-- Visible content -->
					<div class="ui basic segment">
						<h2 class="ui header">
							<i class="edit icon"></i>
							<!-- <div class="content">MR - Admin</div> -->
							<div class="content"><?php echo 'Manuscript &ndash; ' . $manuscript_display_name; ?></div>
						</h2>
					</div>
					<div class="ui teal segment">
						<!-- <h4 class="ui dividing header">Top Header</h4> -->
						<!-- TOP CONTENT HERE -->
						<h4 class="ui dividing header">Section</h4>
						<div class="ui top attached tabular menu">
							<?php if ($f3->get('MR_CONFIG')->debug === true): ?>
							<div class="active link item" data-tab="debug">Debug</div>
							<?php endif; ?>
							<div class="link <?php echo ($f3->get('MR_CONFIG')->debug === false ? 'active' : '') ?> item" data-tab="presentation">Presentation</div>
							<div class="link item" data-tab="images">Images</div>
							<div class="link item" data-tab="partners">Partners</div>
							<div class="link item" data-tab="html">HTML</div>
							<div class="link item" data-tab="xml">XML</div>
						</div>
					</div>
					<div class="ui violet segment">
						<!-- <h4 class="ui dividing header">Middle Header</h4> -->
						<!-- MIDDLE CONTENT HERE -->
						<?php if ($f3->get('MR_CONFIG')->debug === true): ?>
						<div class="ui active tab segment" data-tab="debug">
							<h4 class="ui dividing header">Debug</h4>
							<pre><code><?php print_r($f3); ?></code></pre>
						</div>
						<?php endif; ?>
						<div class="ui <?php echo ($f3->get('MR_CONFIG')->debug === false ? 'active' : '') ?> tab segment" data-tab="presentation">
							<h4 class="ui dividing header">Presentation</h4>
							<!-- <pre><code><?php // print_r($manuscript->getAllMetas()); ?></code></pre> -->

							<?php
							// Generate form
							$html_form  = '<form class="ui form presentation" method="POST">' . PHP_EOL;
							// $html_form .= '<div class="two fields">' . PHP_EOL;

							foreach ($manuscript->getAllMetas() as $key => $value) {
								$html_form .= '<div class="required field">' . PHP_EOL;
								$html_form .= '<label>' . $key . '</label>' . PHP_EOL;

								switch ($key) {
									case 'dcterm-abstract':
										$html_form .= '<textarea id="' . str_replace('dcterm-', '', $key) . '" name="' . $key . '" placeholder="' . $key . '" required>' . $value . '</textarea>' . PHP_EOL;
										break;

									case 'dcterm-creator':
										$html_form .= '<select class="ui search selection dropdown" id="' . str_replace('dcterm-', '', $key) . '" name="' . $key . '" multiple required>' . PHP_EOL;
										$html_form .= '<option value="">' . ucfirst(str_replace('dcterm-', '', $key)) . '</option>' . PHP_EOL;
										foreach ($manuscript->getMetas($key) as $creator) {
											$html_form .= '<option value="' . $creator . '" selected>' . $creator . '</option>' . PHP_EOL;
										}
										$html_form .= '</select>' . PHP_EOL;
										break;

									case 'dcterm-isPartOf':
									case 'dcterm-isVersionOf':
									case 'dcterm-isReferencedBy':
									case 'dcterm-hasVersion':
									case 'dcterm-hasFormat':
									case 'dcterm-license':
										$html_form .= '<input type="url" id="' . str_replace('dcterm-', '', $key) . '" name="' . $key . '" placeholder="' . $key . '" value="' . $value . '" required>' . PHP_EOL;
										break;

									default:
										$html_form .= '<input type="text" id="' . str_replace('dcterm-', '', $key) . '" name="' . $key . '" placeholder="' . $key . '" value="' . $value . '" required>' . PHP_EOL;
										break;
								}

								$html_form .= '</div>' . PHP_EOL;
							}

							// $html_form .= '</div>' . PHP_EOL;
							$html_form .= '</form>' . PHP_EOL;
							echo $html_form;
							?>

						</div>
						<div class="ui tab segment" data-tab="images">
							<h4 class="ui dividing header">Images</h4>
							<!-- <pre><code><?php // print_r($manuscript->contentsFolios()) ?></code></pre> -->
							<!-- <div class="ui placeholder segment"> -->
							<div class="ui segment">
								<div class="ui one column very relaxed grid">
									<div class="middle aligned column">
										<div class="ui special eight stackable cards">

											<?php
											// Fake iiif page counter
											$test_iiif_page = 0;
											foreach ($manuscript->contentsFolios() as $manuscript_folio) {
												// Increment fake iiif page counter
												$test_iiif_page++;

												// Caching values for gaining in performance
												$folio_id            = $manuscript_folio->id;
												$folio_name          = $manuscript_folio->getFolioName();
												$folio_image         = $manuscript_folio->getFolioImage();
												if ($folio_image) {
													$folio_image_content = $folio_image->imageContent();
													$folio_image_type = $folio_image->imageType();
													$folio_image_text = $folio_image->getCopyrightText();
												}

												// Image card
												$html_card_image  = '<div class="card">' . PHP_EOL;
												$html_card_image .= "\t" . '<div class="blurring dimmable image">' . PHP_EOL;
												$html_card_image .= "\t\t" . '<div class="ui dimmer">' . PHP_EOL;
												$html_card_image .= "\t\t\t" . '<div class="content">' . PHP_EOL;
												$html_card_image .= "\t\t\t\t" . '<div class="center">' . PHP_EOL;
												$html_card_image .= "\t\t\t\t\t" . '<div class="ui inverted button" onclick="$(\'.ui.edit.image.folio-' . $folio_id . '.modal\').modal(\'show\');">' . (!$folio_image ? 'Add' : 'Edit') . ' Image</div>' . PHP_EOL;
												$html_card_image .= "\t\t\t\t" . '</div>' . PHP_EOL;
												$html_card_image .= "\t\t\t" . '</div>' . PHP_EOL;
												$html_card_image .= "\t\t" . '</div>' . PHP_EOL;
												if ($folio_image) {
													if ($f3->get('MR_CONFIG')->images->format->base64 === true) {
														$html_card_image .= "\t\t" . '<img src="data:' . $folio_image_type . ';base64,' . $folio_image_content . '" style="height: 100px;">' . PHP_EOL;
													}
													else {
														$html_card_image .= "\t\t" . '<img src="' . $f3->get('MR_PATH_WEB') . 'api/iiif/2-1/images/' . $manuscript->name . '-page' . $test_iiif_page . '/full/100,/0/default.jpg" style="height: 100px;" loading="lazy">' . PHP_EOL;
													}
												}
												else {
													// $html_card_image .= "\t\t" . '<img data-src="holder.js/214x100?random=yes&text=' . substr($manuscript->name, 0, 2) . '">' . PHP_EOL;
													$html_card_image .= "\t\t" . '<img data-src="holder.js/214x100?random=yes&text=Missing Image">' . PHP_EOL;
												}
												$html_card_image .= "\t" . '</div>' . PHP_EOL;
												$html_card_image .= "\t" . '<div class="content center aligned">' . PHP_EOL;
												$html_card_image .= "\t\t" . '<a class="header tooltipped" title="View Image" data-position="bottom center" onclick="$(\'.ui.view.image.folio-' . $folio_id . '.modal\').modal(\'show\');">' . $folio_name . '</a>' . PHP_EOL;
												$html_card_image .= "\t" . '</div>' . PHP_EOL;
												$html_card_image .= '</div>' . PHP_EOL;
												echo $html_card_image;

												// Image modal edit
												$html_modal_image_edit  = '<div class="ui edit image folio-' . $folio_id . ' overlay fullscreen modal">' . PHP_EOL;
												$html_modal_image_edit .= "\t" . '<i class="close icon"></i>' . PHP_EOL;
												$html_modal_image_edit .= "\t" . '<div class="header">' . $folio_name . '</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t" . '<div class="content">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t" . '<div class="ui segment">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t" . '<div class="ui two column very relaxed grid">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t" . '<div class="middle aligned column">' . PHP_EOL;
												if ($folio_image) {
													if ($f3->get('MR_CONFIG')->images->format->base64 === true) {
														$html_modal_image_edit .= "\t\t\t\t\t" . '<img class="image updateable" src="data:' . $folio_image_type . ';base64,' . $folio_image_content . '" style="margin: 0 auto; width: 62%; height: auto;">' . PHP_EOL;
													}
													else {
														$html_modal_image_edit .= "\t\t\t\t\t" . '<img class="image updateable" src="' . $f3->get('MR_PATH_WEB') . 'api/iiif/2-1/images/' . $manuscript->name . '-page' . $test_iiif_page . '/full/full/0/default.jpg" style="margin: 0 auto; width: 62%; height: auto;" loading="lazy">' . PHP_EOL;
													}
												}
												else {
													$html_modal_image_edit .= "\t\t\t\t\t" . '<img class="image deleteable" data-src="holder.js/640x480?random=yes&text=' . $folio_name . '">' . PHP_EOL;
													$html_modal_image_edit .= "\t\t\t\t\t" . '<img class="image updateable" src="null" style="visibility: hidden;">' . PHP_EOL;
												}
												$html_modal_image_edit .= "\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t" . '<div class="top aligned column">' . PHP_EOL;
												// $html_modal_image_edit .= "\t\t\t\t\t" . '<form class="ui form" method="POST" enctype="multipart/form-data" onsubmit="return false;">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t" . '<form class="ui form" onsubmit="return false;">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t" . '<input type="hidden" name="manuscript_folio_id" value="' . $folio_id . '">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t" . '<input type="hidden" name="manuscript_folio_image_content" id="manuscript_folio_image_content_' . $folio_id . '">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t" . '<input type="hidden" name="manuscript_folio_image_metas" id="manuscript_folio_image_metas_' . $folio_id . '">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t" . '<h4 class="ui dividing header">Image Details</h4>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t" . '<div class="required field">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t" . '<label>Image</label>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t" . '<input type="file" name="manuscript_folio_image" id="manuscript_folio_image_' . $folio_id . '" placeholder="Image file" accept="image/jpeg" required>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t" . '<div class="ui pointing blue basic label transition hidden" id="manuscript_folio_image_entry_' . $folio_id . '">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t\t" . 'Size' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t\t" . '<div class="detail" id="manuscript_folio_image_size_' . $folio_id . '"></div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t" . '<div class="inline fields transition hidden" id="manuscript_folio_image_type_' . $folio_id . '">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t" . '<label class="required" for="manuscript_folio_image_type">Type</label>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t" . '<div class="field">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t\t" . '<div class="ui radio checkbox original">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t\t\t" . '<input type="radio" name="manuscript_folio_image_type" value="original" tabindex="0" class="hidden">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t\t\t" . '<label>Original</label>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t" . '<div class="field">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t\t" . '<div class="ui radio checkbox copyrighted">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t\t\t" . '<input type="radio" name="manuscript_folio_image_type" value="copyrighted" tabindex="0" class="hidden">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t\t\t" . '<label>Copyrighted</label>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t" . '<div class="required field">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t" . '<label>Text</label>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t" . '<textarea class="manuscript folio image copyright" name="manuscript_folio_image_copyright" placeholder="Copyright text to put on the image" required>' . $folio_image_text . '</textarea>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t" . '<div class="field" data-tooltip="Feature disabled for the moment" data-position="top center">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t" . '<label>Position</label>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t" . '<div class="ui selection disabled dropdown">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t\t" . '<input type="hidden" name="position">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t\t" . '<i class="dropdown icon"></i>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t\t" . '<div class="default text">Select text position</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t\t" . '<div class="menu">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t\t\t" . '<div class="item" data-value="top">Top</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t\t\t" . '<div class="item active" data-value="bottom">Bottom</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t" . '<div class="ui center aligned basic segment">' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t" . '<div class="ui primary submit folio image button" data-action="manuscript image upload" data-id="' . $params['id'] . '">' . (!$folio_image ? 'Add' : 'Update') . '</div>' . PHP_EOL;
												// $html_modal_image_edit .= "\t\t\t\t\t\t\t" . '<div class="ui primary folio image button" data-action="manuscript image upload" data-id="' . $params['id'] . '">' . (!$folio_image ? 'Add' : 'Update') . '</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t" . '<div class="ui reset button">Reset</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t" . '<div class="ui clear button">Clear</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t\t" . '<div class="ui close button" onclick="$(\'.ui.edit.image.folio-' . $folio_id . '.modal\').modal(\'hide\');">Close</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t\t" . '</form>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t\t" . '<div class="ui vertical divider uncut"></div>' . PHP_EOL;
												$html_modal_image_edit .= "\t\t" . '</div>' . PHP_EOL;
												$html_modal_image_edit .= "\t" . '</div>' . PHP_EOL;
												$html_modal_image_edit .= '</div>' . PHP_EOL;
												echo $html_modal_image_edit;

												// Image modal view
												$html_modal_image_view  = '<div class="ui view image folio-' . $folio_id . ' modal">' . PHP_EOL;
												$html_modal_image_view .= "\t" . '<i class="close icon"></i>' . PHP_EOL;
												$html_modal_image_view .= "\t" . '<div class="header">' . $folio_name . '</div>' . PHP_EOL;
												$html_modal_image_view .= "\t" . '<div class="image content">' . PHP_EOL;
												if ($folio_image) {
													if ($f3->get('MR_CONFIG')->images->format->base64 === true) {
														$html_modal_image_view .= "\t\t" . '<img class="image" src="data:' . $folio_image_type . ';base64,' . $folio_image_content . '" style="margin: 0 auto; width: 62%; height: auto;">' . PHP_EOL;
													}
													else {
														$html_modal_image_view .= "\t\t" . '<img class="image" src="' . $f3->get('MR_PATH_WEB') . 'api/iiif/2-1/images/' . $manuscript->name . '-page' . $test_iiif_page . '/full/full/0/default.jpg" style="margin: 0 auto; width: 62%; height: auto;" loading="lazy">' . PHP_EOL;
													}
												}
												$html_modal_image_view .= "\t\t" . '<div class="description">' . PHP_EOL;
												$html_modal_image_view .= "\t\t\t" . '<p>' . $folio_image_text . '</p>' . PHP_EOL;
												$html_modal_image_view .= "\t\t" . '</div>' . PHP_EOL;
												$html_modal_image_view .= "\t" . '</div>' . PHP_EOL;
												$html_modal_image_view .= '</div>' . PHP_EOL;
												echo $html_modal_image_view;
											}
											?>

										</div>
									</div>
									<!-- <div class="middle aligned column">
										<div class="ui big green button" onclick="$('.ui.upload.image.modal').modal('show');">
											<i class="upload icon"></i>
											Upload
										</div>
									</div> -->
								</div>
								<!-- <div class="ui vertical divider">
									Or
								</div> -->
							</div>
						</div>
						<div class="ui tab segment" data-tab="partners">
							<h4 class="ui dividing header">Partners</h4>
							<!-- <pre><code><?php // print_r($manuscript->contentPartners()) ?></code></pre> -->
							<div class="ui placeholder segment">
							<!-- <div class="ui segment"> -->
								<div class="ui two column very relaxed grid">
									<div class="middle aligned column">
										<div class="ui special four stackable cards">

											<?php
											foreach ($manuscript->contentPartners() as $manuscript_partner_image) {
												// Caching values for gaining in performance
												$partner_id            = $manuscript_partner_image->id;
												$partner_name          = $manuscript->getMeta('dcterm-provenance');
												$partner_image_content = $manuscript_partner_image->imageContent();
												$partner_image_type    = $manuscript_partner_image->imageType();
												$partner_image_url     = $manuscript_partner_image->url;

												// Partner card
												$html_card_partner  = '<div class="card">' . PHP_EOL;
												$html_card_partner .= "\t" . '<div class="blurring dimmable image">' . PHP_EOL;
												$html_card_partner .= "\t\t" . '<div class="ui dimmer">' . PHP_EOL;
												$html_card_partner .= "\t\t\t" . '<div class="content">' . PHP_EOL;
												$html_card_partner .= "\t\t\t\t" . '<div class="center">' . PHP_EOL;
												$html_card_partner .= "\t\t\t\t\t" . '<div class="ui inverted button" onclick="$(\'.ui.edit.partner.id-' . $partner_id . '.modal\').modal(\'show\');">Edit Partner</div>' . PHP_EOL;
												$html_card_partner .= "\t\t\t\t" . '</div>' . PHP_EOL;
												$html_card_partner .= "\t\t\t" . '</div>' . PHP_EOL;
												$html_card_partner .= "\t\t" . '</div>' . PHP_EOL;
												if ($manuscript_partner_image) {
													$html_card_partner .= "\t\t" . '<img src="data:' . $partner_image_type . ';base64,' . $partner_image_content . '" style="height: 100px;">' . PHP_EOL;
												}
												else {
													$html_card_partner .= "\t\t" . '<img data-src="holder.js/214x100?random=yes&text=Missing Image">' . PHP_EOL;
												}
												$html_card_partner .= "\t" . '</div>' . PHP_EOL;
												$html_card_partner .= "\t" . '<div class="content center aligned">' . PHP_EOL;
												$html_card_partner .= "\t\t" . '<a class="header tooltipped" title="Visit ' . $partner_name . '" data-position="bottom center" href="' . $partner_image_url . '" target="_blank">' . $partner_image_url . '</a>' . PHP_EOL;
												$html_card_partner .= "\t" . '</div>' . PHP_EOL;
												$html_card_partner .= '</div>' . PHP_EOL;
												echo $html_card_partner;

												// Partner modal
												$html_modal_partner  = '<div class="ui edit partner id-' . $partner_id . ' overlay fullscreen modal">' . PHP_EOL;
												$html_modal_partner .= "\t" . '<i class="close icon"></i>' . PHP_EOL;
												$html_modal_partner .= "\t" . '<div class="header">' . $partner_name . '</div>' . PHP_EOL;
												$html_modal_partner .= "\t" . '<div class="content">' . PHP_EOL;
												// $html_modal_partner .= "\t\t" . '<div class="ui placeholder segment">' . PHP_EOL;
												$html_modal_partner .= "\t\t" . '<div class="ui segment">' . PHP_EOL;
												$html_modal_partner .= "\t\t\t" . '<div class="ui two column very relaxed grid">' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t" . '<div class="middle aligned column">' . PHP_EOL;
												if ($manuscript_partner_image) {
													$html_modal_partner .= "\t\t\t\t\t" . '<img class="image updateable" src="data:' . $partner_image_type . ';base64,' . $partner_image_content . '" style="margin: 0 auto; width: 62%; height: auto;">' . PHP_EOL;
												}
												else {
													$html_modal_partner .= "\t\t\t\t\t" . '<img class="image deleteable" data-src="holder.js/640x480?random=yes&text=Missing partner image">' . PHP_EOL;
													$html_modal_partner .= "\t\t\t\t\t" . '<img class="image updateable" src="null" style="visibility: hidden;">' . PHP_EOL;
												}
												$html_modal_partner .= "\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t" . '<div class="top aligned column">' . PHP_EOL;
												// $html_modal_partner .= "\t\t\t\t\t" . '<form class="ui form" method="POST" enctype="multipart/form-data" onsubmit="return false;">' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t" . '<form class="ui form" onsubmit="return false;">' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t" . '<input type="hidden" name="manuscript_partner_id" value="' . $partner_id . '">' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t" . '<input type="hidden" name="manuscript_partner_image_content" id="manuscript_partner_image_content_' . $partner_id . '">' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t" . '<input type="hidden" name="manuscript_partner_image_metas" id="manuscript_partner_image_metas_' . $partner_id . '">' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t" . '<h4 class="ui dividing header">Partner Details</h4>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t" . '<div class="' . (!$manuscript_partner_image ? 'required ' : '') . 'field">' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t\t" . '<label>Image</label>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t\t" . '<input type="file" name="manuscript_partner_image" id="manuscript_partner_image_' . $partner_id . '" placeholder="Image file" accept="image/png"' . (!$manuscript_partner_image ? ' required' : '') . '>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t\t" . '<div class="ui pointing blue basic label transition hidden" id="manuscript_partner_image_entry_' . $partner_id . '">' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t\t\t" . 'Size' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t\t\t" . '<div class="detail" id="manuscript_partner_image_size_' . $partner_id . '"></div>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t" . '</div>' . PHP_EOL;
												// $html_modal_partner .= "\t\t\t\t\t\t" . '<div class="ui fluid action input">' . PHP_EOL;
												// $html_modal_partner .= "\t\t\t\t\t\t\t" . '<input type="url" name="manuscript_partner_url" placeholder="Enter partner URL" value="' . $partner_image_url . '">' . PHP_EOL;
												// $html_modal_partner .= "\t\t\t\t\t\t\t" . '<button class="ui primary button">Update</button>' . PHP_EOL;
												// $html_modal_partner .= "\t\t\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t" . '<div class="required field">' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t\t" . '<label>Url</label>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t\t" . '<input type="url" name="manuscript_partner_url" placeholder="Enter partner URL" value="' . $partner_image_url . '">' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t" . '<div class="ui center aligned basic segment">' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t\t" . '<div class="ui primary submit partner image button" data-action="partner image upload" data-id="' . $params['id'] . '">' . (!$folio_image ? 'Add' : 'Update') . '</div>' . PHP_EOL;
												// $html_modal_partner .= "\t\t\t\t\t\t\t" . '<div class="ui primary partner image button" data-action="partner image upload" data-id="' . $params['id'] . '">' . (!$folio_image ? 'Add' : 'Update') . '</div>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t\t" . '<div class="ui reset button">Reset</div>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t\t" . '<div class="ui clear button">Clear</div>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t\t" . '<div class="ui close button" onclick="$(\'.ui.edit.partner.id-' . $partner_id . '.modal\').modal(\'hide\');">Close</div>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t" . '</form>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t" . '<div class="ui vertical divider uncut"></div>' . PHP_EOL;
												$html_modal_partner .= "\t\t" . '</div>' . PHP_EOL;
												$html_modal_partner .= "\t" . '</div>' . PHP_EOL;
												$html_modal_partner .= '</div>' . PHP_EOL;
												echo $html_modal_partner;
											}
											?>

										</div>
									</div>
									<div class="middle aligned column">
										<div class="ui big green button" onclick="$('.ui.add.partner.modal').modal('show');">
											<i class="add icon"></i>
											Add
										</div>
									</div>
								</div>
								<div class="ui vertical divider">
									Or
								</div>
							</div>
						</div>
						<div class="ui tab segment" data-tab="html">
							<h4 class="ui dividing header">HTML</h4>
							<!-- <pre><code><?php // print_r($manuscript->contentsHtml()) ?></code></pre> -->
							<div class="ui grid">
								<div class="four wide column">
									<div class="ui vertical fluid tabular menu">
										<?php
										$index_a = 0; // Used for initial "active" state
										foreach ($manuscript->contentsHtml() as $content_html) {
											$html_folio_id = $content_html->id;
											echo '<a class="item' . ($index_a === 0 ? ' active' : '') . '" data-tab="html/file-' . $html_folio_id . '">' . $content_html->name . '</a>' . PHP_EOL;
											$index_a++;
										}
										?>
									</div>
								</div>
								<div class="twelve wide stretched column">
									<?php
									$index_b = 0; // Used for initial "active" state
									foreach ($manuscript->contentsHtml() as $content_html) {
										$html_folio_id = $content_html->id;

										$html_folio_area  = '<div class="ui tab basic fitted vertically segment' . ($index_b === 0 ? ' active' : '') . '" data-tab="html/file-' . $html_folio_id . '" style="margin-top: 0;">' . PHP_EOL;
										$html_folio_area .= "\t" . '<form class="ui form" method="POST">' . PHP_EOL;
										$html_folio_area .= "\t\t" . '<input type="hidden" name="manuscript_folio_html_id" value="' . $html_folio_id . '">' . PHP_EOL;
										$html_folio_area .= "\t\t" . '<div class="field">' . PHP_EOL;
										$html_folio_area .= "\t\t\t" . '<label>Content</label>' . PHP_EOL;
										if ($content_html->getLangCode()) {
											$html_folio_area .= "\t\t\t" . '<textarea name="manuscript_folio_html" rows="30" style="font-family: ' . $f3->get('MR_CONFIG')->languages->{$content_html->getLangCode()}->font . '">' . $content_html->content . '</textarea>' . PHP_EOL;
										}
										else {
											$html_folio_area .= "\t\t\t" . '<textarea name="manuscript_folio_html" rows="30">' . $content_html->content . '</textarea>' . PHP_EOL;
										}
										$html_folio_area .= "\t\t" . '</div>' . PHP_EOL;
										$html_folio_area .= "\t" . '</form>' . PHP_EOL;
										$html_folio_area .= '</div>' . PHP_EOL;
										echo $html_folio_area;

										$index_b++;
									}
									?>
								</div>
							</div>
							<!-- <pre><code><?php // echo htmlentities(print_r($manuscript->contentsHtml(), true)); ?></code></pre> -->
							<!-- <pre><code><?php // echo htmlentities(print_r($manuscript->contents(), true)); ?></code></pre> -->
							<!-- <pre><code><?php // echo htmlentities(print_r($manuscript->contents()->getAlteredHtml(), true)); ?></code></pre> -->
						</div>
						<div class="ui tab segment" data-tab="xml">
							<h4 class="ui dividing header">XML</h4>
							<!-- <pre><code><?php // print_r($manuscript->contentsMeta()) ?></code></pre> -->
							<div class="ui grid">
								<div class="four wide column">
									<div class="ui vertical fluid tabular menu">
										<?php
										$index_c = 0; // Used for initial "active" state
										foreach ($manuscript->contentsMeta() as $content_meta) {
											$xml_folio_id = $content_meta->id;
											echo '<a class="item' . ($index_c === 0 ? ' active' : '') . '" data-tab="xml/file-' . $xml_folio_id . '">' . $content_meta->name . '</a>' . PHP_EOL;
											$index_c++;
										}
										?>
									</div>
								</div>
								<div class="twelve wide stretched column">
									<?php
									$index_d = 0; // Used for initial "active" state
									foreach ($manuscript->contentsMeta() as $content_meta) {
										$xml_folio_id = $content_meta->id;

										$xml_folio_area  = '<div class="ui tab basic fitted vertically segment' . ($index_d === 0 ? ' active' : '') . '" data-tab="xml/file-' . $xml_folio_id . '" style="margin-top: 0;">' . PHP_EOL;
										$xml_folio_area .= "\t" . '<form class="ui form" method="POST">' . PHP_EOL;
										$xml_folio_area .= "\t\t" . '<input type="hidden" name="manuscript_folio_xml_id" value="' . $xml_folio_id . '">' . PHP_EOL;
										$xml_folio_area .= "\t\t" . '<div class="field">' . PHP_EOL;
										$xml_folio_area .= "\t\t\t" . '<label>Content</label>' . PHP_EOL;
										$xml_folio_area .= "\t\t\t" . '<textarea name="manuscript_folio_xml" rows="30">' . $content_meta->content . '</textarea>' . PHP_EOL;
										$xml_folio_area .= "\t\t" . '</div>' . PHP_EOL;
										$xml_folio_area .= "\t" . '</form>' . PHP_EOL;
										$xml_folio_area .= '</div>' . PHP_EOL;
										echo $xml_folio_area;

										$index_d++;
									}
									?>
								</div>
							</div>
						</div>
					</div>
					<div class="ui blue center aligned segment">
						<button class="ui green stateful button sync" data-tooltip="Feature disabled for the moment" data-position="top center" onclick="return false;" style="display: none;">Save</button>
						<!-- <button class="ui green stateful button sync" onclick="$('.ui.form:visible').form('submit');" style="display: none;">Sync from Nakala</button> -->
						<button class="ui green stateful button sub" onclick="$('.ui.form:visible').form('submit');" style="display: none;">Submit</button>
						<button class="ui stateful button gen" onclick="$('.ui.form:visible').form('reset');">Reset</button>
						<button class="ui stateful button gen" onclick="$('.ui.form:visible').form('clear');">Clear</button>
						<a class="ui button" href="<?php echo $f3->get('BASE') . str_replace('edit', 'view', $f3->get('PATH')); ?>">View</a>
					</div>
				</div>

				<?php endif; ?>

			</main>

			<?php require_once $f3->get('MR_PATH') . '/adm/ui/modals.php'; ?>
			<?php require_once $f3->get('MR_PATH') . '/adm/ui/footer.php'; ?>