/**
* Main text-viewer code
* Jonathan Barda / SIB - 2020
*/
(function () {
    "use strict";

    console.group('Loading');
    console.log('Loading text-viewer.js');
    console.groupEnd();

    /* Fix '$' not defined when mirador jquery-migrate is loaded */
    window.jQ = window.jQ || (typeof $.noConflict !== 'undefined' ? $.noConflict(true) : $);

    /* Init when DOM is ready */
    jQ(function () {
        // Get iframe
        // var iframe_element = document.getElementsByTagName('iframe')[0];
        var $iframe = jQ('.small-iframe');

        // Parse current URL
        var parsedURL = new URL(window.location.href);

        console.group('URL Parser');
        console.log('Parsed URL:', parsedURL);
        console.groupEnd();

        console.group('TextViewer');
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
        console.groupEnd();

        // Get API path
        var apiURLv1 = mr.getBaseURL() + 'api/mr/v' 
            // + (typeof apiUrlVersion !== 'undefined' && apiUrlVersion ? apiUrlVersion : '1')
            + (typeof apiUrlVersion !== 'undefined' && apiUrlVersion ? apiUrlVersion : mr.api.text.version)
            + '?id=' + manuscriptID;

        console.group('URL Parser');
        console.log('API URL v1:', apiURLv1);
        console.groupEnd();

        // Get related manuscript JSON data
        jQ.getJSON(apiURLv1)
            .done(function (data) {
                console.group('API - Text');
                console.log('Returned manuscript:', data);
                
                // Parsing returned folios
                var totalFolios = data.folios.length;
                
                if (parsedURL.searchParams.has('folio') === true) {
                    
                    console.log('Total folios:', totalFolios);
                    console.info('Lookup for defined folio...');

                    jQ.each(data.folios, function (index, folio) {
                        if (folio === decodedFolioID) {
                            console.info('Taking selected folio:');
                            console.info(' - Folio index: ', index);
                            console.info(' - Folio name: ', folio);

                            // Build folio URL
                            var folioURL = mr.getBaseURL() + 'view?id=' + manuscriptID + '&folio=' + btoa(folio) + '&alter=true';
                            console.log('Encoded folio ID:', btoa(folio));
                            console.log('Folio URL:', folioURL);
    
                            // Now display corresponding folio
                            console.info('Displaying folio...');
                            // iframe_element.src = folioURL;
                            $iframe.attr('src', folioURL);
                        }
                    });
                }
                else {
                    console.log('Total folios:', totalFolios);
                    console.info('Lookup for default folio...');

                    jQ.each(data.folios, function (index, folio) {
                        if (!folio.includes('_ENG') && !folio.includes('_FRA')) {
                            console.info('Taking first folio found:');
                            console.info(' - Folio index: ', index);
                            console.info(' - Folio name: ', folio);

                            // Build folio URL
                            var folioURL = mr.getBaseURL() + 'view?id=' + manuscriptID + '&folio=' + btoa(folio) + '&alter=true';
                            console.log('Encoded folio ID:', btoa(folio));
                            console.log('Folio URL:', folioURL);

                            // Now display first folio
                            console.info('Displaying folio...');
                            // iframe_element.src = folioURL;
                            $iframe.attr('src', folioURL);

                            // Leave the loop
                            return false;
                        }
                    });
                }

                // Hide loading status
                $iframe.one('load', function(event) {
                    console.group('iframe');
                    console.log('iframe loaded.', event);
                    jQ('#small-iframe-loading-status').addClass('hide');
                    console.groupEnd();
                });

                console.groupEnd();
            })
            .fail(function (jqxhr, textStatus, error) {
                console.group('API');
                console.error('[' + textStatus + '],', error, jqxhr);
                console.groupEnd();
            });
    });
})()