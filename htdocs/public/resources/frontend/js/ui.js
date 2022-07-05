/**
* Main client side UI code
* Jonathan Barda / SIB - 2021
*/
"use strict";

/* IFFE */
(function () {
    "use strict";

    console.group('Loading');
    console.log('Loading ui.js');
    console.groupEnd();

    /* Fix '$' not defined when mirador jquery-migrate is loaded */
    window.jQ = window.jQ || (typeof $.noConflict !== 'undefined' ? $.noConflict(true) : $);

    /* Init when DOM is ready */
    jQ(function () {
        /* Toasts */
        jQ('.toast').toast();

        /* Tooltips */
        jQ('[data-toggle="tooltip"]').tooltip();
    
        /* Modals */
        jQ('[data-toggle="modal"]').modal({
        // $('#abstract-content').modal({
            keyboard: false,
            show: false
        });

        /* Modals::Callbacks */
        jQ('#abstract-content').on('show.bs.modal', function(event) {
            console.group('Modals');
            console.log('Modal open.', event);
            console.groupEnd();
        });
        jQ('#abstract-content').on('hidden.bs.modal', function(event) {
            console.group('Modals');
            console.log('Modal closed.', event);
            console.groupEnd();
        });
        jQ('#large-manuscript').on('show.bs.modal', function(event) {
            console.group('Modals');
            console.log('Modal open.', event);

            // Fixing weird bootstrap bug
            var delayedFix = setTimeout(function(){
                $('body').css('padding-right', '');
                clearTimeout(delayedFix);
            }, 0);

            // Cache iframes
            var $iframeSmall = jQ('.small-iframe'),
                $iframeLarge = jQ('.large-iframe');

            // Display loading status
            jQ('#large-iframe-loading-status').removeClass('hide');

            // Hide loading status
            $iframeLarge.one('load', function(event) {
                console.group('iframe');
                console.log('iframe loaded.', event);
                jQ('#large-iframe-loading-status').addClass('hide');
                console.groupEnd();
            });

            // Assign iframe src from small to large one
            $iframeLarge.attr('src', $iframeSmall.attr('src'));

            // Display assigned iframes src
            console.log('Small iframe src:', $iframeSmall.attr('src'));
            console.log('Large iframe src:', $iframeLarge.attr('src'));

            console.groupEnd();
        });
        jQ('#large-manuscript').on('hidden.bs.modal', function(event) {
            console.group('Modals');
            console.log('Modal closed.', event);

            // Remove assigned iframe src on the large one
            $('.large-iframe').removeAttr('src');

            // Fixing weird bootstrap bug
            var delayedFix = setTimeout(function(){
                $('body').css('padding-right', '');
                clearTimeout(delayedFix);
            }, 0);

            console.groupEnd();
        });
    });
})();
