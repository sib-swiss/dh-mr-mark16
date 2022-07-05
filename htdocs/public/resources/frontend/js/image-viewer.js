/**
* Main image-viewer code
* Jonathan Barda / SIB - 2020
*/
(function () {
    "use strict";

    console.group('Loading');
    console.log('Loading image-viewer.js');
    console.groupEnd();

    /* Fix '$' not defined when mirador jquery-migrate is loaded */
    window.jQ = window.jQ || (typeof $.noConflict !== 'undefined' ? $.noConflict(true) : $);

    /* Init when DOM is ready */
    jQ(function () {
        // Console output
        console.group('ImageViewer');

        // Parse current URL
        var parsedURL = new URL(window.location.href);
        console.log('Parsed URL:', parsedURL);

        // Get current manuscript ID
        var manuscriptID = parsedURL.searchParams.get('id');
        var decodedManuscriptID = String(atob(manuscriptID)).toUpperCase();
        console.log('Manuscript ID:', manuscriptID);
        console.log('Decoded manuscript:', decodedManuscriptID);

        // Get manuscript folio ID
        if (parsedURL.searchParams.has('folio') === true) {
            var manuscriptFolioID = parsedURL.searchParams.get('folio');
            var decodedFolioID = atob(manuscriptFolioID);
            console.log('Manuscript folio ID:', manuscriptFolioID);
            console.log('Decoded manuscript folio:', decodedFolioID);
        }

       
        // End console group
        console.groupEnd();

        // Get related collection JSON data
        jQ.getJSON(mr.getApiUrl() + '/collection/Mark16')
            .done(function (data) {
                console.group('API - Image');
                console.log('Returned collection:', data);

                // Store collection to main app object
                if (window.mr) {
                    console.log('Stored collection:', true);
                    window.mr.setCollection(data);
                    window.mr.getState();
                }

                console.groupEnd();
            })
            .fail(function (jqxhr, textStatus, error) {
                console.group('API');
                console.error(textStatus + ', ' + error, jqxhr);
                console.groupEnd();
            });

        // Get related manifest JSON data
        jQ.getJSON(mr.getApiUrl() + '/' + decodedManuscriptID + '/manifest')
            .done(function (data) {
                console.group('API - Image');
                console.log('Returned manifest:', data);

                // Store manifest to main app object
                if (window.mr) {
                    console.log('Stored manifest:', true);
                    window.mr.setManifest(data);
                    window.mr.getState();
                }

                console.groupEnd();
            })
            .fail(function (jqxhr, textStatus, error) {
                console.group('API');
                console.error(textStatus + ', ' + error, jqxhr);
                console.groupEnd();
            });
    });
})()