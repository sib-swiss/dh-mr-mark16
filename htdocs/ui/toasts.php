<?php if ($app_config->debug === true): ?>

        <div id="loading-time" class="toast" role="status" aria-live="polite" aria-atomic="true" data-delay="5000" style="position: fixed; top: 105; right: 0;">
            <div class="toast-header">
                <!-- <img src="..." class="rounded mr-2" alt="..."> -->
                <strong class="mr-auto">Loading time</strong>
                <small class="text-muted">just now</small>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body">
                <?php $app->get_load_duration(); ?>
            </div>
        </div>

<?php endif; ?>