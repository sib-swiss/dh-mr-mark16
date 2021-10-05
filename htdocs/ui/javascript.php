
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script type="text/javascript" src="resources/js/jquery-3.4.1.min.js"></script>
        <script type="text/javascript" src="resources/js/popper.min.js"></script>
        <script type="text/javascript" src="resources/js/bootstrap-4.3.1.min.js"></script>

        <!-- Holder.js -->
        <script type="text/javascript" src="resources/js/holder-v2.9.0/holder.min.js"></script>

        <!-- EventEmitter -->
        <script type="text/javascript" src="resources/js/event-emitter.js"></script>

        <?php if ($app_config->debug === true): ?>

        <!-- Debug code -->
        <script type="text/javascript" src="resources/js/debug.js"></script>

        <?php endif; ?>

        <?php if (isset($page_options) && $page_options->old_code === true): ?>

        <!-- Old app code -->
        <script type="text/javascript" src="resources/js/manuscript.js"></script>
        <!-- <script type="text/javascript" src="resources/js/NTTF.js"></script> -->

        <?php else: ?>

        <!-- New app code -->
        <script type="text/javascript" src="resources/js/app.js"></script>

        <?php endif; ?>

        <?php if (isset($page_options) && $page_options->mirador === true): ?>

        <!-- Mirador -->
        <script type="text/javascript" src="<?php echo 'resources/js/mirador-v' . $app_config->mirador->version . '/mirador.js'; ?>"></script>
        <script type="text/javascript" src="resources/js/text-viewer.js"></script>
        <script type="text/javascript" src="resources/js/image-viewer.js"></script>

        <?php endif; ?>