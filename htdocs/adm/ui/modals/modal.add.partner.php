
			<!-- Modal: Add - Partner -->
			<div class="ui add partner overlay fullscreen modal">
				<i class="close icon"></i>
				<div class="header">Add Partner</div>
				<div class="content">
					<!-- <div class="ui placeholder segment"> -->
					<div class="ui segment">
						<div class="ui two column very relaxed grid">
							<div class="middle aligned column">
								<img class="image deleteable" data-src="holder.js/640x480?random=yes&text=New Partner">
								<img class="image updateable" src="null" style="visibility: hidden;">
							</div>
							<div class="top aligned column">
								<!-- <form class="ui form" method="POST" enctype="multipart/form-data" onsubmit="return false;"> -->
								<form class="ui form" onsubmit="return false;">
									<input type="hidden" name="manuscript_id" value="<?php echo $params['id']; ?>">
									<!-- TODO: Replace this value by the FUI API -->
									<input type="hidden" name="manuscript_add_partner" value="true">
									<!-- END TODO -->
									<input type="hidden" name="manuscript_partner_image_content" id="manuscript_partner_image_content_<?php echo (isset($manuscript) ?: $manuscript->id); ?>">
									<input type="hidden" name="manuscript_partner_image_metas" id="manuscript_partner_image_metas_<?php echo (isset($manuscript) ?: $manuscript->id); ?>">
									<h4 class="ui dividing header">Partner Details</h4>
									<div class="required field">
										<label>Image</label>
										<input type="file" name="manuscript_partner_image" id="manuscript_partner_image_<?php echo (isset($manuscript) ?: $manuscript->id); ?>" placeholder="Image file" accept="image/png" required>
										<div class="ui pointing blue basic label transition hidden" id="manuscript_partner_image_entry_<?php echo (isset($manuscript) ?: $manuscript->id); ?>">
											Size
											<div class="detail" id="manuscript_partner_image_size_<?php echo (isset($manuscript) ?: $manuscript->id); ?>"></div>
										</div>
									</div>
									<div class="required field">
										<label>Url</label>
										<input type="url" name="manuscript_partner_url" placeholder="Enter partner URL" required>
									</div>
									<div class="ui center aligned basic segment">
										<div class="ui primary submit partner details button" data-action="partner add" data-id="<?php echo $params['id']; ?>">Add</div>
										<!-- <div class="ui primary partner details button" data-action="partner add" data-id="<?php echo $params['id']; ?>">Add</div> -->
										<div class="ui reset button">Reset</div>
										<div class="ui clear button">Clear</div>
										<div class="ui close button" onclick="$('.ui.add.partner.modal').modal('hide');">Close</div>
									</div>
								</form>
							</div>
						</div>
						<div class="ui vertical divider uncut"></div>
					</div>
				</div>
			</div>
