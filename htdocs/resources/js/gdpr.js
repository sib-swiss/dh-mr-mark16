/**
* Main client side UI code
* Jonathan Barda / SIB - 2021
*/
"use strict";

/* IFFE */
(function () {
    "use strict";

    console.group('Loading');
    console.log('Loading gdpr.js');
    console.groupEnd();

    /* Fix '$' not defined when mirador jquery-migrate is loaded */
    window.jQ = window.jQ || (typeof $.noConflict !== 'undefined' ? $.noConflict(true) : $);

    /* Init when DOM is ready */
    jQ(function () {
        console.log('cookie.gdpr', Cookies.get('gdpr'));
        if (1 != Cookies.get('gdpr')) {
            jQ("#gdpr_banner").show();
        }

        jQ("#gdpr_btn").click(function () {
            Cookies.set('gdpr', 1)
            jQ("#gdpr_banner").hide();
        })

    });
})();