<?php use classes\Models\Manuscript; ?>

			<!-- Top Static Menu -->
			<header class="ui inverted vertical segment">
				<div class="ui fluid container">
					<div class="ui large secondary inverted menu">
						<a class="toc item"><i class="sidebar icon"></i></a>
						<div class="header link item large screen only">
							<a href="<?php echo $f3->get('MR_PATH_WEB') . 'admin'; ?>"><i class="glasses icon"></i>MR - Admin</a>
						</div>
						<div class="link item large screen only">
							<a onclick="$('.ui.sidebar').sidebar('toggle');"><i class="bars icon"></i>Menu</a>
						</div>
						<div class="header link item center aligned mobile only"><a href="<?php echo $f3->get('MR_PATH_WEB') . 'admin'; ?>"><i class="glasses icon"></i>MR - Admin</a></div>
						<div class="right menu">
							<div class="ui item animated labeled icon button" id="dark-theme">
								<div class="visible content">
									<i class="moon icon"></i>
									Dark
								</div>
								<div class="hidden content">
									<i class="sun icon"></i>
									Light
								</div>
							</div>
							<div class="ui item animated labeled icon button" id="light-theme">
								<div class="visible content">
									<i class="sun icon"></i>
									Light
								</div>
								<div class="hidden content">
									<i class="moon icon"></i>
									Dark
								</div>
							</div>
							<a class="item" onclick="$('.ui.add.manuscript.modal').modal('show');"><i class="upload icon"></i>Add Manuscript</a>
							<a class="item cache-action" data-action="clear cache"><i class="eraser icon"></i>Clear Cache</a>
							<a class="item" href="<?php echo $f3->get('MR_PATH_WEB') . 'admin/dba?sqlite=&username=&db=' . urlencode($f3->get('MR_DATA_DIR') . '/' . $f3->get('MR_CONFIG')->db->file); ?>" target="_blank"><i class="database icon"></i>Database</a>
							<a class="item" onclick="window.location.reload();"><i class="redo icon"></i>Refresh</a>
							<div class="ui inverted dropdown item">
								<i class="question circle icon"></i> Help <i class="dropdown icon"></i>
								<div class="menu">
									<a class="item" href="<?php echo $f3->get('MR_PATH_WEB') . 'admin/help'; ?>" target="_blank">
										<i class="plug icon"></i>Manual
									</a>
									<a class="item" href="https://fatfreeframework.com/" target="_blank">
										<i class="plug icon"></i>Framework
									</a>
								</div>
							</div>
							<div class="ui inverted dropdown item">
								<i class="language icon"></i> Lang <i class="dropdown icon"></i>
								<div class="menu">
									<a class="left aligned item"><i class="us flag"></i>English</a>
									<a class="left aligned item"><i class="fr flag"></i>French</a>
									<a class="left aligned item"><i class="ru flag"></i>Russian</a>
									<a class="left aligned item"><i class="es flag"></i>Spanish</a>
									<a class="left aligned item"><i class="ch flag"></i>Swiss French</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</header>

			<?php if ($f3->get('PATTERN') !== '/admin'): ?>

			<!-- Breadcrumbs -->
			<div class="ui vertical basic fitted segment">
				<div class="ui basic vertically fitted segment">
					<!-- <div class="ui basic segment"> -->
						<div class="ui breadcrumb">

							<?php
							$parsed_path = explode('/', $f3->get('PATH'));
							$parsed_path_parts = count($parsed_path);
							$max_display_parts = 3;
							$breads_html = '';
							for ($i = 0; $i < $parsed_path_parts; $i++) {
								// First part
								if ($i === 0) {
									$breads_html .= '<a href="' . $f3->get('MR_PATH_WEB') . $parsed_path[$i] . '" class="section">Home</a>' . PHP_EOL;
								}

								// Last part
								elseif ($i === ($parsed_path_parts-1)) {
									if (isset($params['id'])) {
										if ($m = Manuscript::findBy('name', base64_decode($parsed_path[$i]))) {
											// echo '<!--' . PHP_EOL;
											// echo base64_decode($parsed_path[$i]);
											// print_r($m);
											// print_r($m->name);
											// print_r($m->getDisplayname());
											// echo '-->' . PHP_EOL;
		
											$breads_html .= '<div class="divider"> / </div>' . PHP_EOL;
											$breads_html .= '<div class="active section">' . $m->getDisplayname() . '</div>' . PHP_EOL;
										}
										else {
											$breads_html .= '<div class="divider"> / </div>' . PHP_EOL;
											$breads_html .= '<div class="active section">Error: Manuscript not found</div>' . PHP_EOL;
										}
									}
									else {
										$breads_html .= '<div class="divider"> / </div>' . PHP_EOL;
										$breads_html .= '<div class="active section">' . ucfirst($parsed_path[$i]) . '</div>' . PHP_EOL;
									}
								}

								// All other parts
								else {
									// Skip extra parts first
									if ($i === $max_display_parts) { continue; }

									// Generate requested parts
									$breads_html .= '<div class="divider"> / </div>' . PHP_EOL;
									$breads_html .= '<a href="' . $f3->get('MR_PATH_WEB') . ($i !== 1 ? $parsed_path[1] . '/' : '') . $parsed_path[$i] . '" class="section">' . ucfirst($parsed_path[$i]) . '</a>' . PHP_EOL;
								}
							}
							echo $breads_html;
							?>

						</div>
					<!-- </div> -->
				</div>
			</div>

			<?php endif; ?>