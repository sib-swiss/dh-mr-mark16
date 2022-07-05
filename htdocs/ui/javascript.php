
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script type="text/javascript" src="resources/frontend/js/jquery-3.4.1.min.js"></script>
        <script type="text/javascript" src="resources/frontend/js/popper.min.js"></script>
        <script type="text/javascript" src="resources/frontend/js/bootstrap-4.3.1.min.js"></script>

        <!-- Holder.js -->
        <script type="text/javascript" src="resources/frontend/js/holder-v2.9.0/holder.min.js"></script>

        <!-- EventEmitter -->
        <script type="text/javascript" src="resources/frontend/js/event-emitter.js"></script>

        <?php if ($app_config->debug === true): ?>

        <!-- Debug code -->
        <script type="text/javascript" src="resources/frontend/js/debug.js"></script>

        <?php endif; ?>

        <script type="text/javascript" src="resources/frontend/js/app.js"></script>


        <?php if (isset($page_options) && $page_options->mirador === true): ?>

        <!-- Mirador -->
        <script type="text/javascript" src="<?php echo 'resources/frontend/js/mirador-v' . $app_config->mirador->version . '/mirador.js'; ?>"></script>
        <script type="text/javascript" src="resources/frontend/js/text-viewer.js"></script>
        <script type="text/javascript" src="resources/frontend/js/image-viewer.js"></script>

        <?php endif; ?>