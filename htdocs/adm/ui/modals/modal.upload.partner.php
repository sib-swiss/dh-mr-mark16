
			<!-- Modal: Upload - Partner Image -->
			<div class="ui upload partner modal">
				<i class="close icon"></i>
				<div class="header">Add Partner Image</div>
				<div class="scrolling content">
					<!-- <div class="ui padded center aligned segment">
						<div class="ui massive buttons">
							<button class="ui massive teal button"><i class="folder icon"></i>Upload folder</button>
							<div class="or"></div>
							<button class="ui massive blue button"><i class="file archive icon"></i>Upload zip file</button>
						</div>
					</div>
					<h4 class="ui horizontal divider header">
						<i class="image icon"></i>
						Upload image file
					</h4> -->
					<!-- <div class="ui padded left aligned segment"> -->
					<div class="ui basic padded left aligned segment">
						<h4 class="ui horizontal divider header">
							<i class="image icon"></i>
							Upload image file
						</h4>
						<form class="ui form" method="POST" onsubmit="return false;">
							<input type="hidden" name="manuscript_id" value="<?php echo $params['id']; ?>">
							<!-- TODO: Replace this value by the FUI API -->
							<input type="hidden" name="manuscript_add_partner" value="true">
							<!-- END TODO -->
							<input type="hidden" name="manuscript_partner_image_content" id="manuscript_partner_image_content_<?php echo $manuscript->id; ?>">
							<input type="hidden" name="manuscript_partner_image_metas" id="manuscript_partner_image_metas_<?php echo $manuscript->id; ?>">
							<div class="ui accordion field">
								<div class="title">
									<i class="icon dropdown"></i>
									Using URL
								</div>
								<div class="content field">
									<div class="ui action input">
										<input type="url" name="manuscript_partner_image_url" id="manuscript_partner_image_url_<?php echo $manuscript->id; ?>" placeholder="Partner image URL">
										<button class="ui blue download right labeled icon button">
											<i class="download icon"></i>
											Download
										</button>
									</div>
								</div>
							</div>
							<div class="ui accordion field">
								<div class="title">
									<i class="icon dropdown"></i>
									Using local file
								</div>
								<div class="content field">
									<div class="ui action input">
										<input type="file" name="manuscript_partner_image" id="manuscript_partner_image_file_<?php echo $manuscript->id; ?>" placeholder="Partner image file" accept="image/png">
										<button class="ui blue upload right labeled icon button" data-action="partner image upload" data-id="<?php echo $params['id']; ?>">
											<i class="upload icon"></i>
											Upload
										</button>
									</div>
									<div class="ui pointing blue basic label" id="manuscript_partner_image_entry_<?php echo $manuscript->id; ?>" style="display: none;">
										Size
										<div class="detail" id="manuscript_partner_image_size_<?php echo $manuscript->id; ?>"></div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
