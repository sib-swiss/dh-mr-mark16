<?php
// Default page options
$page_options = new stdClass();
$page_options->title = 'Nakala Parser';
?>
<?php require_once $f3->get('MR_PATH') . '/adm/ui/header.php'; ?>

<?php require_once $f3->get('MR_PATH') . '/adm/ui/nav-follow.php'; ?>

<?php require_once $f3->get('MR_PATH') . '/adm/ui/sidebar.php'; ?>

		<!-- Page Contents -->
		<div class="pusher">

			<?php require_once $f3->get('MR_PATH') . '/adm/ui/nav.php'; ?>

			<!-- Main content-->
			<main class="ui vertical basic very padded segment">
				<div id="variable-container" class="ui container">
					<div class="ui basic segment">
						<h2 class="ui header">
							<i class="glasses icon"></i>
							<div class="content">MR - Admin</div>
						</h2>
					</div>
					<div class="ui secondary segment">
						<h4 class="ui header">Nakala Parser</h4>

						<?php
						if ($f3->get('VERB') === 'POST') {
							// Test received data
							/* echo '<pre style="width: 95%; margin: 1em auto; overflow: auto;">POST: ' . print_r($f3->get('POST'), true) . '</pre>' . PHP_EOL; */

							if (isset($_POST['nakala_url']) && !empty($_POST['nakala_url'])) {
								$nakala_url = (filter_var($_POST['nakala_url'], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)
									? filter_var($_POST['nakala_url'], FILTER_SANITIZE_URL)
									: null
								);

								// Test received URL
								/* echo '<pre style="width: 95%; margin: 1em auto; overflow: auto;">' . PHP_EOL;
								echo 'Received URL:' . PHP_EOL;
								var_dump($nakala_url);
								echo '</pre>' . PHP_EOL; */

								if (!is_null($nakala_url)) {
									// Compose URLS
									$nakala_parsed_url = parse_url($nakala_url);
									if (strpos($nakala_url, 'api.') !== false && strpos($nakala_url, '/datas/') !== false) {
										$nakala_api_url = $nakala_url;
										$nakala_download_url = str_replace('/datas/', '/data/', $nakala_url);
									}
									else {
										$nakala_api_url = $nakala_parsed_url['scheme'] . '://api.' . $nakala_parsed_url['host'] . '/datas' . $nakala_parsed_url['path'];
										$nakala_download_url = $nakala_parsed_url['scheme'] . '://api.' . $nakala_parsed_url['host'] . '/data' . $nakala_parsed_url['path'];
									}

									// Tests composed URLS
									/* echo '<pre style="width: 95%; margin: 1em auto; overflow: auto;">Parsed URL: ' . print_r($nakala_parsed_url, true) . '</pre>' . PHP_EOL;
									echo '<pre style="width: 95%; margin: 1em auto; overflow: auto;">API URL: ' . $nakala_api_url . '</pre>' . PHP_EOL; */

									// Load experimental Nakala Parser
									require $f3->get('MR_PATH') . '/classes/nakala.php';

									// Init Nakala Parser
									$nakala_parser = new Nakala();
									$nakala_parser->set_url($nakala_api_url);

									// XML Data
									$nakala_xml = $nakala_parser->get_xml();

									// JSON Data
									$nakala_json = $nakala_parser->get_json();
									$nakala_parsed_json = $nakala_parser->parse_json();
									$nakala_parsed_files = $nakala_parser->get_files();
									$nakala_parsed_metas = $nakala_parser->get_metas();
									$nakala_converted_metas = $nakala_parser->convert_metas();

									// Tests Parser
									/* echo '<pre style="width: 95%; height: 100px; margin: 1em auto; overflow: auto;">' . PHP_EOL;
									echo 'Raw XML:' . PHP_EOL;
									var_dump(htmlentities($nakala_xml));
									echo '</pre>' . PHP_EOL;
									echo '<pre style="width: 95%; height: 100px; margin: 1em auto; overflow: auto;">' . PHP_EOL;
									echo 'Raw JSON:' . PHP_EOL;
									var_dump($nakala_json);
									echo '</pre>' . PHP_EOL;
									echo '<pre style="width: 95%; height: 200px; margin: 1em auto; overflow: auto;">' . PHP_EOL;
									echo 'Parsed JSON:' . PHP_EOL;
									var_dump($nakala_parsed_json);
									echo '</pre>' . PHP_EOL;
									echo '<pre style="width: 95%; height: 200px; margin: 1em auto; overflow: auto;">' . PHP_EOL;
									echo 'Parsed JSON Files:' . PHP_EOL;
									var_dump($nakala_parsed_files);
									echo '</pre>' . PHP_EOL;
									echo '<pre style="width: 95%; height: 200px; margin: 1em auto; overflow: auto;">' . PHP_EOL;
									echo 'Parsed JSON Metas:' . PHP_EOL;
									var_dump($nakala_parsed_metas);
									echo '</pre>' . PHP_EOL;
									echo '<pre style="width: 95%; height: 200px; margin: 1em auto; overflow: auto;">' . PHP_EOL;
									echo 'Converted JSON Metas:' . PHP_EOL;
									var_dump($nakala_converted_metas);
									echo '</pre>' . PHP_EOL; */
									?>

						<div class="ui two column very relaxed grid">
							<div class="column">
								<div class="ui list">
									<div class="item">
										<i class="folder icon"></i>
										<div class="content">
											<div class="header"><?php echo $nakala_parser->get_meta('dcterm-bibliographicCitation'); ?></div>
											<div class="description">Manuscript</div>
											<div class="list">

												<?php
												$list_html = '';
												foreach ($nakala_parsed_files as $nakala_file) {
													echo '<!--' . PHP_EOL;
													print_r($nakala_file);
													echo '-->' . PHP_EOL;

													$list_html .= '<div class="item">' . PHP_EOL;

													switch ($nakala_file->extension) {
														case 'html':
															$list_html .= "\t" . '<i class="file alternate icon"></i>' . PHP_EOL;
															break;

														case 'xml':
															$list_html .= "\t" . '<i class="file code icon"></i>' . PHP_EOL;
															break;

														default:
															$list_html .= "\t" . '<i class="file icon"></i>' . PHP_EOL;
															break;
													}

													$list_html .= "\t" . '<div class="content">' . PHP_EOL;
													$list_html .= "\t\t" . '<div class="header"><a href="' . $nakala_download_url . '/' . $nakala_file->sha1 . '" target="_blank">' . $nakala_file->name . '</a></div>' . PHP_EOL;

													switch ($nakala_file->extension) {
														case 'html':
															$list_html .= "\t\t" . '<div class="description">HTML file</div>' . PHP_EOL;
															break;

														case 'xml':
															$list_html .= "\t\t" . '<div class="description">XML metadata file</div>' . PHP_EOL;
															break;

														default:
															$list_html .= "\t\t" . '<div class="description">Generic file</div>' . PHP_EOL;
															break;
													}

													$list_html .= "\t" . '</div>' . PHP_EOL;
													$list_html .= '</div>' . PHP_EOL;
												}

												echo $list_html;
												?>

											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="column">
								<table class="ui fixed single line collapsing table" style="width: 100%;">
									<tbody>

										<?php
										$table_html = '';
										foreach ($nakala_converted_metas as $nakala_meta) {
											/* echo '<!--' . PHP_EOL;
											print_r($nakala_meta);
											echo '-->' . PHP_EOL; */

											$table_html .= '<tr>' . PHP_EOL;
											$table_html .= "\t" . '<td>' . str_replace('dcterm-', '', array_keys($nakala_meta)[0]) . '</td>' . PHP_EOL;
											if (stripos(array_values($nakala_meta)[0], 'http') !== false) {
												$table_html .= "\t" . '<td class="tooltipped" title="' . array_values($nakala_meta)[0] . '"><a href="' . array_values($nakala_meta)[0] . '" target="_blank">' . array_values($nakala_meta)[0] . '</a></td>' . PHP_EOL;
											}
											else {
												$table_html .= "\t" . '<td class="tooltipped" title="' . array_values($nakala_meta)[0] . '">' . array_values($nakala_meta)[0] . '</td>' . PHP_EOL;
											}
											$table_html .= '</tr>' . PHP_EOL;
										}
										echo $table_html;
										?>
										
									</tbody>
								</table>
							</div>
						</div>
						<div class="ui vertical divider">
							and
						</div>

									<?php
								}
							}
						}
						?>

					</div>
					<div class="ui blue center aligned segment">
						<!-- <h4 class="ui dividing header">Nakala Parser</h4> -->
						<!-- MIDDLE CONTENT HERE -->
						<!-- <button class="ui green button">Download Manuscript</button> -->
						<!-- <button class="ui green button" onclick="<?php echo 'window.open(\'download/' . base64_encode($nakala_api_url) . '\');'; ?>">Download Manuscript</button> -->
						<button class="ui green download manuscript button" data-action="nakala download" data-id="<?php echo base64_encode($nakala_api_url); ?>">Download Manuscript</button>
					</div>
					<!-- <div class="ui tertiary segment">
						<h4 class="ui dividing header">Bottom Header</h4>
						BOTTOM CONTENT HERE
					</div> -->
				</div>
			</main>

			<?php require_once $f3->get('MR_PATH') . '/adm/ui/modals.php'; ?>
			<?php require_once $f3->get('MR_PATH') . '/adm/ui/footer.php'; ?>