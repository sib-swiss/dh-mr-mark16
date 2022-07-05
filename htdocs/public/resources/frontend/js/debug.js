"use strict";

/**
* Main debug code
* Jonathan Barda / SIB - 2020
*/
(function () {
    "use strict";

    console.group('Loading');
    console.log('Loading debug.js');
    console.groupEnd();

    /* Fix '$' not defined when mirador jquery-migrate is loaded */
    window.jQ = window.jQ || (typeof $.noConflict !== 'undefined' ? $.noConflict(true) : $);

    /* Init when DOM is ready */
    jQ(function () {
        // Display loading toast
        jQ('#loading-time').toast('show');
    });
})()