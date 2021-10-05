	
		<!-- Top Following Menu -->
		<div class="ui top fixed menu transition hidden">
			<div class="ui fluid container">
				<div class="header link item">
					<a href="<?php echo $f3->get('MR_PATH_WEB') . 'admin'; ?>"><i class="glasses icon"></i>MR - Admin</a>
					<a onclick="$('.ui.sidebar').sidebar('toggle');" style="margin-left: 40px;"><i class="bars icon"></i>Menu</a>
				</div>
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
				</div>
			</div>
		</div>
