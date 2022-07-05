
			<!-- Footer -->
			<footer class="ui inverted vertical footer segment">
				<div class="ui container">
					<div class="ui stackable inverted divided equal height grid">
						<div class="ten wide column">
							<h4 class="ui inverted header"><i class="glasses icon"></i>MR - Admin</h4>
							<p>Welcome to the MARK16 manuscript room. It is the first part of a <em>virtual research environment</em> (VRE) devoted to the last chapter of the Gospel according to Mark, developed in the framework of the Swiss National Science Foundation PRIMA project <a href="http://p3.snf.ch/project-179755" target="_blank">MARK16</a>.</p>
						</div>
						<div class="three wide column">
							<h4 class="ui inverted header">About</h4>
							<div class="ui inverted link list">
								<a class="item" href="https://gitlab.sib.swiss/mark16-vre-group/manuscript" target="_blank">Project</a>
								<a class="item" href="https://gitlab.sib.swiss/mark16-vre-group/manuscript/-/blob/master/NEW-OPERATION-GUIDE.md">Documentation</a>
							</div>
						</div>
						<div class="three wide column">
							<h4 class="ui inverted header">Credits</h4>
							<div class="ui inverted link list">
								<a class="item" href="https://sib.swiss/" target="_blank">SIB</a>
								<a class="item" href="https://mark16.sib.swiss/" target="_blank">MARK16</a>
								<a class="item" href="https://mr-mark16.sib.swiss/" target="_blank">Manuscript Room</a>
								<a class="item" href="https://fomantic-ui.com/" target="_blank">Fomantic-UI</a>
							</div>
						</div>
					</div>
					<div class="ui section divider"></div>
					<div class="ui horizontal inverted divided link list">
						<div class="item">
							<span>&copy; 2020 - </span>
							<a href="https://sib.swiss/" target="_blank">SIB</a> / 
							<a href="https://mark16.sib.swiss/" target="_blank">MARK16</a>
						</div>
					</div>
				</div>
			</footer>
		</div>
		
		<!-- Fomantic-UI -->
		<script type="text/javascript" id="jquery-js" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
		<script type="text/javascript" id="fomantic-ui-js" src="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.8.7/semantic.min.js" integrity="sha512-1Nyd5H4Aad+OyvVfUOkO/jWPCrEvYIsQENdnVXt1+Jjc4NoJw28nyRdrpOCyFH4uvR3JmH/5WmfX1MJk2ZlhgQ==" crossorigin="anonymous"></script>
		<!-- Including Dependencies -->
		<script type="text/javascript" id="jquery-address-js" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.address/1.6/jquery.address.min.js" integrity="sha512-Fhm8fcAQhENO1HmU1JjbnNm6ReszFIiJvkHdnuGZBznaaM6vakH4YEPO7v8M3PbGR03R/dur0QP5vZ5s4YaN7w==" crossorigin="anonymous"></script>
		<script type="text/javascript" id="jquery-serialize-obj-js" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-serialize-object/2.5.0/jquery.serialize-object.min.js" integrity="sha512-Gn0tSSjkIGAkaZQWjx3Ctl/0dVJuTmjW/f9QyB302kFjU4uTNP4HtA32U2qXs/TRlEsK5CoEqMEMs7LnzLOBsA==" crossorigin="anonymous"></script>
		<!-- <script type="text/javascript" id="page-js" src="https://cdnjs.cloudflare.com/ajax/libs/page.js/1.11.6/page.js" integrity="sha512-MkYIEFfyoRmnQFt8ZoTflIGLT8RR+PfZSHtsG5Knc5uFayAspGft8XTaMIOozqD4KkGzE6xa7jU+tfWtcXMqtg==" crossorigin="anonymous"></script> -->
		<!-- Including Dark Theme -->
		<script type="text/javascript" id="dark-ui-js" src="<?php echo $f3->get('MR_PATH_WEB') . 'resources/backend/js/dark-fomantic-ui.js'; ?>"></script>
		<!-- Including UI code -->
		<script type="text/javascript" id="ui-wrappers-js" src="<?php echo $f3->get('MR_PATH_WEB') . 'resources/backend/js/ui-wrappers.js'; ?>"></script>
		<script type="text/javascript" id="ui-js" src="<?php echo $f3->get('MR_PATH_WEB') . 'resources/backend/js/ui.js'; ?>"></script>
		<!-- Including App code -->
		<script type="text/javascript" id="app-js" src="<?php echo $f3->get('MR_PATH_WEB') . 'resources/backend/js/app.js'; ?>"></script>
		<!-- Including API code -->
		<script type="text/javascript" id="api-js" src="<?php echo $f3->get('MR_PATH_WEB') . 'resources/backend/js/api.js'; ?>"></script>
		<!-- Including Holder.js -->
		<script type="text/javascript" src="<?php echo $f3->get('MR_PATH_WEB') . 'resources/frontend/js/holder-v2.9.0/holder.min.js'; ?>"></script>

		<?php require_once $f3->get('MR_PATH') . '/inc/debug.php'; ?>

	</body>
</html>