
			<!-- Modal: Upload - Manuscript -->
			<div class="ui add manuscript modal">
				<i class="close icon"></i>
				<div class="header">Add Manuscript</div>
				<div class="scrolling content">
					<!-- <div class="ui basic padded center aligned segment"> -->
						<!-- <div class="ui massive buttons">
							<button class="ui massive teal button"><i class="folder icon"></i>Upload folder</button>
							<div class="or"></div>
							<button class="ui massive blue button"><i class="file archive icon"></i>Upload zip file</button>
						</div> -->
					<div class="ui basic padded left aligned segment">
						<h4 class="ui horizontal divider header">
							<i class="tag icon"></i>
							Nakala
						</h4>
						<form class="ui form" action="<?php echo $f3->get('MR_PATH_WEB') . 'admin/parse'; ?>" method="POST">
							<div class="ui accordion field">
								<div class="active title">
									<i class="icon dropdown"></i>
									Using URL
								</div>
								<div class="active content field">
									<div class="ui action input">
										<input type="url" name="nakala_url" id="nakala_url" placeholder="https://nakala.fr/10.34847/nkl.6f83096n">
										<button class="ui blue right labeled icon button">
											<i class="glasses icon"></i>
											Parse
										</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
