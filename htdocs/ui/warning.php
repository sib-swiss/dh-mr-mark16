<?php if ($app_config->showMaintenanceMessage === true): ?>

        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?php if ($app_config->messageSize === "big"): ?>

            <h4 class="alert-heading">Warning</h4>
            <p>The website will be on maintenance on <strong><?php echo $app_config->maintenanceDate; ?></strong>.</p>

            <?php else: ?>

            <strong>Warning</strong> &ndash; The website will be on maintenance on <strong><?php echo $app_config->maintenanceDate; ?></strong>.

            <?php endif; ?>

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

<?php endif; ?>