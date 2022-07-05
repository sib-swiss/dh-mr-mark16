<?php
use classes\Models\Manuscript;

// Default page options
$page_options = new stdClass();
$page_options->title = 'View';

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
else {
	$manuscript = false; // Explicitly set as false to avoid validation issue with php 8.x
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
								<option value="">Select a manuscript to view</option>

								<?php foreach(Manuscript::all(['order' => 'temporal ASC']) as $m): ?>
								<option value="<?php echo $f3->get('MR_PATH_WEB') . 'admin/view/' . base64_encode($m->name); ?>"><?php echo $m->getDisplayname(); ?></option>
								<?php endforeach; ?>

							</select>
						</div>
					</div>
				</div>

				<?php else: ?>

				<div class="ui fluid container basic segment">
					<div class="ui basic segment">
						<h2 class="ui header">
							<i class="glasses icon"></i>
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
					<div class="ui purple segment">
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
							$html_form  = '<div class="ui form">' . PHP_EOL;
							// $html_form .= '<div class="two fields">' . PHP_EOL;

							foreach ($manuscript->getAllMetas() as $key => $value) {
								$html_form .= '<div class="field">' . PHP_EOL;
								$html_form .= '<label>' . $key . '</label>' . PHP_EOL;

								switch ($key) {
									case 'dcterm-abstract':
										$html_form .= '<textarea id="' . str_replace('dcterm-', '', $key) . '" name="' . $key . '" placeholder="' . $key . '" readonly>' . $value . '</textarea>' . PHP_EOL;
										break;

									case 'dcterm-creator':
										$html_form .= '<select class="ui search selection dropdown" id="' . str_replace('dcterm-', '', $key) . '" name="' . $key . '" multiple disabled>' . PHP_EOL;
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
										$html_form .= '<input type="url" id="' . str_replace('dcterm-', '', $key) . '" name="' . $key . '" placeholder="' . $key . '" value="' . $value . '" readonly>' . PHP_EOL;
										break;

									default:
										$html_form .= '<input type="text" id="' . str_replace('dcterm-', '', $key) . '" name="' . $key . '" placeholder="' . $key . '" value="' . $value . '" readonly>' . PHP_EOL;
										break;
								}

								$html_form .= '</div>' . PHP_EOL;
							}

							// $html_form .= '</div>' . PHP_EOL;
							$html_form .= '</div>' . PHP_EOL;
							echo $html_form;
							?>

						</div>
						<div class="ui tab segment" data-tab="images">
							<h4 class="ui dividing header">Images</h4>
							<!-- <pre><code><?php // print_r($manuscript->contentsFolios()) ?></code></pre> -->
							<div class="ui placeholder segment">
								<div class="ui two column very relaxed grid">
									<div class="middle aligned column">
										<div class="ui special four stackable cards">

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
												$html_card_image .= "\t\t\t\t\t" . '<div class="ui inverted button" onclick="$(\'.ui.view.image-' . $folio_id . '.modal\').modal(\'show\');">View Image</div>' . PHP_EOL;
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
													// $html_card_image .= "\t\t" . '<img data-src="holder.js/200x100?random=yes&text=' . substr($manuscript->name, 0, 2) . '">' . PHP_EOL;
													$html_card_image .= "\t\t" . '<img data-src="holder.js/200x100?random=yes&text=Missing Image">' . PHP_EOL;
												}
												$html_card_image .= "\t" . '</div>' . PHP_EOL;
												$html_card_image .= "\t" . '<div class="content center aligned">' . PHP_EOL;
												$html_card_image .= "\t\t" . '<a class="header tooltipped" title="View Image" data-position="bottom center" onclick="$(\'.ui.view.image-' . $folio_id . '.modal\').modal(\'show\');">' . $folio_name . '</a>' . PHP_EOL;
												$html_card_image .= "\t" . '</div>' . PHP_EOL;
												$html_card_image .= '</div>' . PHP_EOL;
												echo $html_card_image;

												// Image modal
												$html_modal_image  = '<div class="ui view image-' . $folio_id . ' modal">' . PHP_EOL;
												$html_modal_image .= "\t" . '<i class="close icon"></i>' . PHP_EOL;
												$html_modal_image .= "\t" . '<div class="header">' . $folio_name . '</div>' . PHP_EOL;
												$html_modal_image .= "\t" . '<div class="image content">' . PHP_EOL;
												if ($folio_image) {
													if ($f3->get('MR_CONFIG')->images->format->base64 === true) {
														$html_modal_image .= "\t\t" . '<img class="image" src="data:' . $folio_image_type . ';base64,' . $folio_image_content . '" style="margin: 0 auto; width: 62%; height: auto;">' . PHP_EOL;
													}
													else {
														$html_modal_image .= "\t\t" . '<img class="image" src="' . $f3->get('MR_PATH_WEB') . 'api/iiif/2-1/images/' . $manuscript->name . '-page' . $test_iiif_page . '/full/full/0/default.jpg" style="margin: 0 auto; width: 62%; height: auto;" loading="lazy">' . PHP_EOL;
													}
												}
												$html_modal_image .= "\t\t" . '<div class="description">' . PHP_EOL;
												$html_modal_image .= "\t\t\t" . '<p>' . $folio_image_text . '</p>' . PHP_EOL;
												$html_modal_image .= "\t\t" . '</div>' . PHP_EOL;
												$html_modal_image .= "\t" . '</div>' . PHP_EOL;
												$html_modal_image .= '</div>' . PHP_EOL;
												echo $html_modal_image;
											}
											?>

										</div>
									</div>
									<div class="middle aligned column">
										<a class="ui big green button" href="<?php echo $f3->get('BASE') . str_replace('view', 'edit', $f3->get('PATH')); ?>#/images">
											<i class="edit icon"></i>
											Edit
										</a>
									</div>
								</div>
								<div class="ui vertical divider">
									Or
								</div>
							</div>
						</div>
						<div class="ui tab segment" data-tab="partners">
							<h4 class="ui dividing header">Partners</h4>
							<!-- <pre><code><?php // print_r($manuscript->contentPartners()) ?></code></pre> -->
							<div class="ui placeholder segment">
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
												$html_card_partner .= "\t\t\t\t\t" . '<div class="ui inverted button" onclick="$(\'.ui.view.partner-' . $partner_id . '.modal\').modal(\'show\');">View Partner</div>' . PHP_EOL;
												$html_card_partner .= "\t\t\t\t" . '</div>' . PHP_EOL;
												$html_card_partner .= "\t\t\t" . '</div>' . PHP_EOL;
												$html_card_partner .= "\t\t" . '</div>' . PHP_EOL;
												if ($manuscript_partner_image) {
													$html_card_partner .= "\t\t" . '<img src="data:' . $partner_image_type . ';base64,' . $partner_image_content . '" style="height: 100px;">' . PHP_EOL;
												}
												else {
													$html_card_partner .= "\t\t" . '<img data-src="holder.js/200x100?random=yes&text=Missing Image">' . PHP_EOL;
												}
												$html_card_partner .= "\t" . '</div>' . PHP_EOL;
												$html_card_partner .= "\t" . '<div class="content center aligned">' . PHP_EOL;
												$html_card_partner .= "\t\t" . '<a class="header tooltipped" title="View Website" data-position="bottom center" href="' . $partner_image_url . '" target="_blank">' . $partner_name . '</a>' . PHP_EOL;
												$html_card_partner .= "\t" . '</div>' . PHP_EOL;
												$html_card_partner .= '</div>' . PHP_EOL;
												echo $html_card_partner;

												// Partner modal
												$html_modal_partner  = '<div class="ui view partner-' . $partner_id . ' overlay fullscreen modal">' . PHP_EOL;
												$html_modal_partner .= "\t" . '<i class="close icon"></i>' . PHP_EOL;
												$html_modal_partner .= "\t" . '<div class="header">' . $partner_name . '</div>' . PHP_EOL;
												$html_modal_partner .= "\t" . '<div class="content">' . PHP_EOL;
												$html_modal_partner .= "\t\t" . '<div class="ui placeholder segment">' . PHP_EOL;
												$html_modal_partner .= "\t\t\t" . '<div class="ui two column very relaxed grid">' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t" . '<div class="middle aligned column">' . PHP_EOL;
												if ($manuscript_partner_image) {
													$html_modal_partner .= "\t\t\t\t\t" . '<img class="image" src="data:' . $partner_image_type . ';base64,' . $partner_image_content . '" style="margin: 0 auto; width: 62%; height: auto;">' . PHP_EOL;
												}
												else {
													$html_modal_partner .= "\t\t\t\t\t" . '<img class="image" data-src="holder.js/640x480?random=yes&text=Missing partner image">' . PHP_EOL;
												}
												$html_modal_partner .= "\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t" . '<div class="top aligned column">' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t" . '<div class="ui form">' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t" . '<h4 class="ui dividing header">Partner Details</h4>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t" . '<div class="ui fluid action input" data-tooltip="Readonly" data-position="bottom right">' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t\t" . '<input type="url" name="manuscript_partner_' . $partner_id . '" placeholder="Enter partner URL" value="' . $partner_image_url . '" readonly>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t\t" . '<button class="ui disabled button">Update</button>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t\t" . '</div>' . PHP_EOL;
												$html_modal_partner .= "\t\t\t\t\t" . '</div>' . PHP_EOL;
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
										<a class="ui big green button" href="<?php echo $f3->get('BASE') . str_replace('view', 'edit', $f3->get('PATH')); ?>#/partners">
											<i class="edit icon"></i>
											Edit
										</a>
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
										$html_folio_area .= "\t" . '<div class="ui form">' . PHP_EOL;
										$html_folio_area .= "\t\t" . '<div class="field">' . PHP_EOL;
										$html_folio_area .= "\t\t\t" . '<label>Content</label>' . PHP_EOL;
										if ($content_html->getLangCode()) {
											$html_folio_area .= "\t\t\t" . '<textarea rows="30" style="font-family: ' . $f3->get('MR_CONFIG')->languages->{$content_html->getLangCode()}->font . '" readonly>' . $content_html->content . '</textarea>' . PHP_EOL;
										}
										else {
											$html_folio_area .= "\t\t\t" . '<textarea rows="30" readonly>' . $content_html->content . '</textarea>' . PHP_EOL;
										}
										$html_folio_area .= "\t\t\t" . '<div class="ui bottom right attached label">Readonly</div>' . PHP_EOL;
										$html_folio_area .= "\t\t" . '</div>' . PHP_EOL;
										$html_folio_area .= "\t" . '</div>' . PHP_EOL;
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
										$xml_folio_area .= "\t" . '<div class="ui form">' . PHP_EOL;
										$xml_folio_area .= "\t\t" . '<div class="field">' . PHP_EOL;
										$xml_folio_area .= "\t\t\t" . '<label>Content</label>' . PHP_EOL;
										$xml_folio_area .= "\t\t\t" . '<textarea rows="30" readonly>' . $content_meta->content . '</textarea>' . PHP_EOL;
										$xml_folio_area .= "\t\t\t" . '<div class="ui bottom right attached label">Readonly</div>' . PHP_EOL;
										$xml_folio_area .= "\t\t" . '</div>' . PHP_EOL;
										$xml_folio_area .= "\t" . '</div>' . PHP_EOL;
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
						<a class="ui button" href="<?php echo $f3->get('BASE') . str_replace('view', 'edit', $f3->get('PATH')); ?>">Edit</a>
						<a class="ui button" href="<?php echo $f3->get('BASE') . '/show?id=' . $params['id']; ?>" target="_blank">View on MR</a>
					</div>
				</div>

				<?php endif; ?>

			</main>

			<?php require_once $f3->get('MR_PATH') . '/adm/ui/modals.php'; ?>
			<?php require_once $f3->get('MR_PATH') . '/adm/ui/footer.php'; ?>