
		<!-- Sidebar Menu -->
		<div class="ui left inverted sidebar vertical menu">
			<div class="header link item">
				<a href="<?php echo $f3->get('MR_PATH_WEB') . 'admin'; ?>">
					<i class="glasses icon" style="float: left; margin: 0 0.35714286em 0 0;"></i>
					MR - Admin
				</a>
			</div>
			<a class="item" onclick="$('.ui.add.manuscript.modal').modal('show');">
				<i class="upload icon" style="float: left; margin: 0 0.35714286em 0 0;"></i>
				Add Manuscript
			</a>
			<a class="item cache-action" data-action="clear cache">
				<i class="eraser icon" style="float: left; margin: 0 0.35714286em 0 0;"></i>
				Clear Cache
			</a>
			<a class="item" href="<?php echo $f3->get('MR_PATH_WEB') . 'admin/dba?sqlite=&username=&db=' . urlencode($f3->get('MR_DATA_DIR') . '/' . $f3->get('MR_CONFIG')->db->file); ?>" target="_blank">
				<i class="database icon" style="float: left; margin: 0 0.35714286em 0 0;"></i>
				Database
			</a>
			<!-- <a class="item">
				<i class="question circle icon" style="float: left; margin: 0 0.35714286em 0 0;"></i>
				Help
			</a> -->
			<a class="item" onclick="window.location.reload();">
				<i class="redo icon" style="float: left; margin: 0 0.35714286em 0 0;"></i>
				Refresh
			</a>
			<div class="item">
				<div class="ui inverted accordion">
					<div class="title">
						<i class="question circle icon" style="float: left; margin: 0 0.35714286em 0 0;"></i>
						Help
						<i class="dropdown icon" style="float: right;"></i>
					</div>
					<div class="content">
						<div class="ui vertical inverted menu">
							<a class="item" href="<?php echo $f3->get('MR_PATH_WEB') . 'admin/f3'; ?>" target="_blank">
								<i class="plug icon" style="float: left; margin: 0 0.35714286em 0 0;"></i>
								Framework
							</a>
							<a class="item" href="https://gitlab.sib.swiss/mark16-vre-group/manuscript/-/issues/new?issue%5Bassignee_id%5D=&issue%5Bmilestone_id%5D=" target="_blank">
								<i class="bug icon" style="float: left; margin: 0 0.35714286em 0 0;"></i>
								Create issue
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="item">
				<div class="ui inverted accordion">
					<div class="title">
						<i class="cog icon" style="float: left; margin: 0 0.35714286em 0 0;"></i>
						Settings
						<i class="dropdown icon" style="float: right;"></i>
					</div>
					<div class="content">
						<div class="ui vertical inverted menu">
							<a class="item" href="<?php echo htmlentities(strip_tags($_SERVER['REQUEST_URI'])) . '/cache'; ?>" target="_blank">
								<i class="layer group icon" style="float: left; margin: 0 0.35714286em 0 0;"></i>
								OPcache GUI
							</a>
							<a class="item" href="<?php echo htmlentities(strip_tags($_SERVER['REQUEST_URI'])) . '/info'; ?>" target="_blank">
								<i class="microchip icon" style="float: left; margin: 0 0.35714286em 0 0;"></i>
								PHP Info
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="item">
				<div class="ui inverted accordion">
					<div class="title">
						<i class="language icon" style="float: left; margin: 0 0.35714286em 0 0;"></i>
						Lang
						<i class="dropdown icon" style="float: right;"></i>
					</div>
					<div class="content">
						<div class="ui vertical inverted menu">
							<a class="item"><i class="us flag"></i>English</a>
							<a class="item"><i class="fr flag"></i>French</a>
							<a class="item"><i class="ru flag"></i>Russian</a>
							<a class="item"><i class="es flag"></i>Spanish</a>
							<a class="item"><i class="ch flag"></i>Swiss French</a>
						</div>
					</div>
				</div>
			</div>
			<a class="item" onclick="$('.ui.sidebar').sidebar('toggle');">
				<i class="arrow left icon" style="float: left; margin: 0 0.35714286em 0 0;"></i>
				Hide menu
			</a>
		</div>
